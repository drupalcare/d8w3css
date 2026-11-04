<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Upgrade;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\RevisionLogInterface;

/**
 * Renames the theme's old classes and ids held in configuration and content.
 *
 * Every method is a dry run unless $apply is TRUE, and returns one row per
 * value it would change or wants checked. Configuration is changed through
 * the config API; content through the entity API, as a new revision.
 *
 * Content is limited to ids (owner decision D3): a class typed into a body
 * field is the editor's, and the font and animation value classes are
 * frozen (D1), so there is nothing else in content the theme renames.
 *
 * The configuration and content methods are copied from Solo Utilities'
 * StoredClassMigrator; a lane drift check keeps their code (docblocks
 * excluded) byte-identical. Only
 * the theme settings and the revision message are this theme's own.
 *
 * @internal
 */
final class StoredClassMigrator {

  /**
   * Views option keys that hold a class list.
   *
   * @var list<string>
   */
  private const array VIEWS_CLASS_KEYS = [
    'css_class',
    'row_class',
    'class',
    'wrapper_class',
    'element_class',
    'element_wrapper_class',
    'element_label_class',
  ];

  /**
   * Formatted text field types scanned with --content.
   *
   * @var list<string>
   */
  private const array TEXT_TYPES = ['text', 'text_long', 'text_with_summary'];

  /**
   * Content entities loaded per batch.
   */
  private const int BATCH = 50;

  /**
   * The settings group whose values are class lists typed by the site.
   */
  private const string COLOR_GROUP = 'region_colors';

  /**
   * Builds the migrator.
   *
   * @param \Drupal\drupal8_w3css_theme\Upgrade\ClassMigrator $engine
   *   The class migration engine.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service, for revision creation times.
   * @param list<string> $themes
   *   This theme and its sub-themes, by machine name.
   */
  public function __construct(
    private readonly ClassMigrator $engine,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityFieldManagerInterface $entityFieldManager,
    private readonly TimeInterface $time,
    private readonly array $themes,
  ) {}

  /**
   * Renames classes in the colour class settings of this theme and sub-themes.
   *
   * @return list<array{store: string, name: string, path: string, before: string, after: string, note: string}>
   *   One row per changed value or review note.
   */
  public function themeSettings(bool $apply): array {
    $rows = [];
    foreach ($this->themes as $theme) {
      $this->rewrite($theme . '.settings', 'theme settings', $apply, $rows, function (array $data, array &$found): array {
        if (isset($data[self::COLOR_GROUP]) && is_array($data[self::COLOR_GROUP])) {
          foreach ($data[self::COLOR_GROUP] as $key => $value) {
            if (is_string($value) && $value !== '') {
              $data[self::COLOR_GROUP][$key] = $this->classList($value, self::COLOR_GROUP . '.' . $key, $found);
            }
          }
        }
        // The same settings under their names from before the settings
        // rename, for a site that has not run the updates yet.
        foreach (SettingsMap::MAP['moved'] as $flat => $move) {
          if (str_starts_with($move['new'], self::COLOR_GROUP . '.') && isset($data[$flat]) && is_string($data[$flat]) && $data[$flat] !== '') {
            $data[$flat] = $this->classList($data[$flat], $flat, $found);
          }
        }
        return $data;
      });
    }
    return $rows;
  }

  /**
   * Renames classes in Block Class settings, Views and text formats.
   *
   * @return list<array{store: string, name: string, path: string, before: string, after: string, note: string}>
   *   One row per changed value or review note.
   */
  public function configuration(bool $apply): array {
    $rows = [];
    foreach ($this->configFactory->listAll('block.block.') as $name) {
      $this->rewrite($name, 'block_class', $apply, $rows, function (array $data, array &$found): array {
        $classes = $data['third_party_settings']['block_class']['classes'] ?? NULL;
        if (is_string($classes)) {
          $data['third_party_settings']['block_class']['classes'] = $this->classList($classes, 'third_party_settings.block_class.classes', $found);
        }
        return $data;
      });
    }
    foreach ($this->configFactory->listAll('views.view.') as $name) {
      $this->rewrite($name, 'Views', $apply, $rows, function (array $data, array &$found): array {
        foreach (array_keys($data['display'] ?? []) as $id) {
          if (isset($data['display'][$id]['display_options']) && is_array($data['display'][$id]['display_options'])) {
            $data['display'][$id]['display_options'] = $this->walkViews($data['display'][$id]['display_options'], 'display.' . $id . '.display_options', $found);
          }
        }
        return $data;
      });
    }
    foreach ($this->configFactory->listAll('filter.format.') as $name) {
      $this->rewrite($name, 'text format', $apply, $rows, function (array $data, array &$found): array {
        $allowed = $data['filters']['filter_html']['settings']['allowed_html'] ?? NULL;
        if (is_string($allowed)) {
          $data['filters']['filter_html']['settings']['allowed_html'] = $this->classAttributes($allowed, 'filters.filter_html.settings.allowed_html', $found);
        }
        return $data;
      });
    }
    // CKEditor 5 keeps its own copy of the allowed tags and its styles; the
    // two must change with the text format or the editor refuses the format.
    foreach ($this->configFactory->listAll('editor.editor.') as $name) {
      $this->rewrite($name, 'text editor', $apply, $rows, function (array $data, array &$found): array {
        if (!isset($data['settings']['plugins']) || !is_array($data['settings']['plugins'])) {
          return $data;
        }
        $plugins = &$data['settings']['plugins'];
        foreach ($plugins['ckeditor5_sourceEditing']['allowed_tags'] ?? [] as $i => $tag) {
          if (is_string($tag)) {
            $plugins['ckeditor5_sourceEditing']['allowed_tags'][$i] = $this->classAttributes($tag, 'ckeditor5_sourceEditing.allowed_tags.' . $i, $found);
          }
        }
        foreach ($plugins['ckeditor5_style']['styles'] ?? [] as $i => $style) {
          if (is_string($style['element'] ?? NULL)) {
            $plugins['ckeditor5_style']['styles'][$i]['element'] = $this->classAttributes($style['element'], 'ckeditor5_style.styles.' . $i, $found);
          }
        }
        unset($plugins);
        return $data;
      });
    }
    return $rows;
  }

  /**
   * Renames the theme's old ids in formatted text and menu link attributes.
   *
   * Each changed entity is saved once, as a new revision when the entity type
   * supports revisions, so the earlier text stays in the revision history.
   *
   * @return list<array{store: string, name: string, path: string, before: string, after: string, note: string}>
   *   One row per changed field value.
   */
  public function content(bool $apply): array {
    $rows = [];
    $ids = array_keys($this->engine->idMap());
    if ($ids === []) {
      return $rows;
    }
    foreach (self::TEXT_TYPES as $type) {
      foreach ($this->entityFieldManager->getFieldMapByFieldType($type) as $entityType => $fields) {
        foreach (array_keys($fields) as $field) {
          $this->contentField($entityType, $field, $ids, $apply, $rows);
        }
      }
    }
    if ($this->entityTypeManager->hasDefinition('menu_link_content')) {
      $this->menuLinks($apply, $rows);
    }
    return $rows;
  }

  /**
   * Scans one formatted text field of one entity type.
   *
   * @param string $entityType
   *   The entity type id.
   * @param string $field
   *   The field name.
   * @param list<string> $ids
   *   Old ids.
   * @param bool $apply
   *   Whether to save the changes.
   * @param list<array<string, string>> $rows
   *   Report rows, appended to.
   */
  private function contentField(string $entityType, string $field, array $ids, bool $apply, array &$rows): void {
    $storage = $this->entityTypeManager->getStorage($entityType);
    $query = $storage->getQuery()->accessCheck(FALSE);
    $or = $query->orConditionGroup();
    foreach ($ids as $id) {
      $or->condition($field . '.value', $id, 'CONTAINS');
    }
    $matches = array_values($query->condition($or)->execute());
    foreach (array_chunk($matches, self::BATCH) as $chunk) {
      foreach ($storage->loadMultiple($chunk) as $entity) {
        if (!$entity instanceof ContentEntityInterface) {
          continue;
        }
        $changed = FALSE;
        foreach (array_keys($entity->getTranslationLanguages()) as $langcode) {
          $translation = $entity->getTranslation($langcode);
          foreach ($translation->get($field) as $delta => $item) {
            foreach (['value', 'summary'] as $property) {
              $text = $item->getValue()[$property] ?? NULL;
              if (!is_string($text) || $text === '') {
                continue;
              }
              $result = $this->engine->migrateHtmlIds($text);
              if ($result['renamed'] === []) {
                continue;
              }
              foreach ($result['renamed'] as $old => $new) {
                $rows[] = self::row('content', $entityType . ':' . $entity->id() . ':' . $langcode, $field . '.' . $delta . '.' . $property, $old, $new);
              }
              $item->set($property, $result['value']);
              $changed = TRUE;
            }
          }
        }
        if ($apply && $changed) {
          $this->saveRevision($entity);
        }
      }
      $storage->resetCache($chunk);
    }
  }

  /**
   * Renames ids in menu link attributes (the link_attributes contrib module).
   *
   * @param bool $apply
   *   Whether to save the changes.
   * @param list<array<string, string>> $rows
   *   Report rows, appended to.
   */
  private function menuLinks(bool $apply, array &$rows): void {
    $storage = $this->entityTypeManager->getStorage('menu_link_content');
    $all = array_values($storage->getQuery()->accessCheck(FALSE)->execute());
    foreach (array_chunk($all, self::BATCH) as $chunk) {
      foreach ($storage->loadMultiple($chunk) as $link) {
        if ($link->get('link')->isEmpty()) {
          continue;
        }
        $item = $link->get('link')->first();
        $options = $item->get('options')->getValue();
        $id = $options['attributes']['id'] ?? NULL;
        $new = is_string($id) ? $this->engine->newId($id) : NULL;
        if ($new === NULL) {
          continue;
        }
        $rows[] = self::row('content', 'menu_link_content:' . $link->id(), 'link.options.attributes.id', (string) $id, $new);
        if ($apply) {
          $options['attributes']['id'] = $new;
          $item->set('options', $options);
          $this->saveRevision($link);
        }
      }
      $storage->resetCache($chunk);
    }
  }

  /**
   * Saves a content entity as a new revision when its type supports it.
   */
  private function saveRevision(ContentEntityInterface $entity): void {
    if ($entity->getEntityType()->isRevisionable()) {
      $entity->setNewRevision(TRUE);
      if ($entity instanceof RevisionLogInterface) {
        $entity->setRevisionLogMessage('W3CSS Theme 3: renamed old theme ids (scripts/migrate-classes.php --content).');
        $entity->setRevisionCreationTime($this->time->getRequestTime());
      }
    }
    $entity->save();
  }

  /**
   * Runs a rewrite callback on one configuration object.
   *
   * @param string $name
   *   The configuration name.
   * @param string $store
   *   The store label for the report.
   * @param bool $apply
   *   Whether to save the changes.
   * @param list<array<string, string>> $rows
   *   Report rows, appended to.
   * @param callable(array<mixed>, list<array{path: string, before: string, after: string, note: string}> &): array<mixed> $callback
   *   Receives the raw data and a findings list by reference; returns data.
   */
  private function rewrite(string $name, string $store, bool $apply, array &$rows, callable $callback): void {
    $config = $this->configFactory->getEditable($name);
    $data = $config->getRawData();
    $found = [];
    $new = $callback($data, $found);
    foreach ($found as $item) {
      $rows[] = self::row($store, $name, $item['path'], $item['before'], $item['after'], $item['note']);
    }
    if ($apply && $new !== $data) {
      $config->setData($new)->save();
    }
  }

  /**
   * Walks Views display options and renames every class-list value.
   *
   * @param array<mixed> $options
   *   Display options.
   * @param string $path
   *   The config path of the options, for the report.
   * @param list<array<string, string>> $found
   *   Findings, appended to.
   *
   * @return array<mixed>
   *   The options.
   */
  private function walkViews(array $options, string $path, array &$found): array {
    foreach ($options as $key => $value) {
      if (is_array($value)) {
        $options[$key] = $this->walkViews($value, $path . '.' . $key, $found);
      }
      elseif (is_string($value) && in_array($key, self::VIEWS_CLASS_KEYS, TRUE) && $value !== '') {
        $options[$key] = $this->classList($value, $path . '.' . $key, $found);
      }
    }
    return $options;
  }

  /**
   * Renames the values of every class attribute in an HTML tag list.
   *
   * @param string $html
   *   The tags.
   * @param string $path
   *   The config path, for the report.
   * @param list<array<string, string>> $found
   *   Findings, appended to.
   */
  private function classAttributes(string $html, string $path, array &$found): string {
    return preg_replace_callback(
      '/(\bclass\s*=\s*")([^"]*)(")/i',
      fn (array $m): string => $m[1] . $this->classList($m[2], $path, $found) . $m[3],
      $html,
    ) ?? $html;
  }

  /**
   * Renames one class list and records what changed or needs checking.
   *
   * @param string $classes
   *   Space-separated classes.
   * @param string $path
   *   The config path, for the report.
   * @param list<array<string, string>> $found
   *   Findings, appended to.
   */
  private function classList(string $classes, string $path, array &$found): string {
    $result = $this->engine->migrateClassList($classes);
    if ($result['renamed'] !== []) {
      $found[] = ['path' => $path, 'before' => $classes, 'after' => $result['value'], 'note' => ''];
    }
    foreach ($result['notes'] as $note) {
      $found[] = ['path' => $path, 'before' => $note['token'], 'after' => $note['new'], 'note' => $note['kind']];
    }
    return $result['value'];
  }

  /**
   * Builds a report row.
   *
   * @return array{store: string, name: string, path: string, before: string, after: string, note: string}
   *   The row. An empty note means a rename; otherwise the review kind.
   */
  private static function row(string $store, string $name, string $path, string $before, string $after, string $note = ''): array {
    return [
      'store' => $store,
      'name' => $name,
      'path' => $path,
      'before' => $before,
      'after' => $after,
      'note' => $note,
    ];
  }

}
