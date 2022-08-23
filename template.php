<?php
/**
 * @file
 * bootstrap5bondi preprocess functions and theme function overrides.
 */

/**
 * Implements hook_css_alter().
 */
function bootstrap5bondi_css_alter(&$css) {
  // Remove bootstrap5bondi' `/css/component/menu-dropdown.css` if using a custom
  // breakpoint.
  if (config_get('menu.settings', 'menu_breakpoint') == 'custom') {
    $path = backdrop_get_path('theme', 'bootstrap5bondi');
    unset($css[$path . '/css/component/menu-dropdown.css']);
  }
}

/**
 * Prepares variables for page templates.
 *
 * @see page.tpl.php
 */
function bootstrap5bondi_preprocess_page(&$variables) {
  $node = menu_get_object();

  // Add the OpenSans font from core on every page of the site.
  backdrop_add_library('system', 'opensans', TRUE);

  // To add a class 'page-node-[nid]' to each page.
  if ($node) {
    $variables['classes'][] = 'page-node-' . $node->nid;
  }

  // To add a class 'view-name-[name]' to each page.
  $view = views_get_page_view();
  if ($view) {
    $variables['classes'][] = 'view-name-' . $view->name;
  }

  // Add breakpoint-specific CSS for dropdown menus.
  $config = config('menu.settings');
  if ($config->get('menu_breakpoint') == 'custom') {
    backdrop_add_css(backdrop_get_path('theme', 'bootstrap5bondi') . '/css/component/menu-dropdown.breakpoint.css', array(
      'group' => CSS_THEME,
      'every_page' => TRUE,
    ));
    backdrop_add_css(backdrop_get_path('theme', 'bootstrap5bondi') . '/css/component/menu-dropdown.breakpoint-queries.css', array(
      'group' => CSS_THEME,
      'every_page' => TRUE,
      'media' => 'all and (min-width: ' . $config->get('menu_breakpoint_custom') . ')',
    ));
  }
}

/**
 * Prepares variables for maintenance page templates.
 *
 * @see maintenance-page.tpl.php
 */
function bootstrap5bondi_preprocess_maintenance_page(&$variables) {
  $css_path = backdrop_get_path('theme', 'bootstrap5bondi') . '/css/component/maintenance.css';
  backdrop_add_css($css_path);
}

/**
 * Prepares variables for layout templates.
 *
 * @see layout.tpl.php
 */
function bootstrap5bondi_preprocess_layout(&$variables) {
  if ($variables['is_front']) {
    // Add a special front-page class.
    $variables['classes'][] = 'layout-front';
    // Add a special front-page template suggestion.
    $original = $variables['theme_hook_original'];
    $variables['theme_hook_suggestions'][] = $original . '__front';
    $variables['theme_hook_suggestion'] = $original . '__front';
  }
}

/**
 * Prepares variables for node templates.
 *
 * @see node.tpl.php
 */
function bootstrap5bondi_preprocess_node(&$variables) {
  if ($variables['status'] == NODE_NOT_PUBLISHED) {
    $name = node_type_get_name($variables['type']);
    $variables['title_suffix']['unpublished_indicator'] = array(
      '#type' => 'markup',
      '#markup' => '<div class="unpublished-indicator">' . t('This @type is unpublished.', array('@type' => $name)) . '</div>',
    );
  }
}

/**
 * Prepares variables for header templates.
 *
 * @see header.tpl.php
 */
function bootstrap5bondi_preprocess_header(&$variables) {
  $logo = $variables['logo'];
  $logo_attributes = $variables['logo_attributes'];

  // Add classes and height/width to logo.
  if ($logo) {
    $logo_wrapper_classes = array();
    $logo_wrapper_classes[] = 'header-logo-wrapper';
    if ($logo_attributes['width'] <= $logo_attributes['height']) {
      $logo_wrapper_classes[] = 'header-logo-tall';
    }

    $variables['logo_wrapper_classes'] = $logo_wrapper_classes;
  }
}

/**
 * Overrides theme_breadcrumb(). Removes &raquo; from markup.
 *
 * @see theme_breadcrumb().
 */
function bootstrap5bondi_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  $output = '';
  if (!empty($breadcrumb)) {
    $output .= '<nav class="breadcrumb">';
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output .= '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    $output .= '<ol><li>' . implode('</li><li>', $breadcrumb) . '</li></ol>';
    $output .= '</nav>';
  }
  return $output;
}

/**
* Override theme_menu_tree__[menu_name]().
*/
function bootstrap5bondi_menu_tree__main_menu($variables) {
  return '<ul class="navbar-nav">' . $variables['tree'] . '</ul>';
}

function bootstrap5bondi_menu_link__main_menu(array $variables) {
  $element = $variables['element'];
  $link = $variables['link'];
  $sub_menu = '';

  $element['#attributes']['class'][] = 'nav-item'; // change here to desired css class name
  $element['#localized_options']['attributes']['class'][] = 'nav-link p-2 p-lg-3';

  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }

  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}


