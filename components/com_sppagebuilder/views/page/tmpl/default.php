<?php
/**
* @package SP Page Builder
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2020 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('jquery.framework');

require_once ( JPATH_COMPONENT .'/parser/addon-parser.php' );
$doc = Factory::getDocument();
$user = Factory::getUser();
$app = Factory::getApplication();
$params = ComponentHelper::getParams('com_sppagebuilder');

if ($params->get('fontawesome', 1))
{
	$doc->addStyleSheet(Uri::base(true) . '/components/com_sppagebuilder/assets/css/font-awesome-5.min.css');
	$doc->addStyleSheet(Uri::base(true) . '/components/com_sppagebuilder/assets/css/font-awesome-v4-shims.css');
}

if (!$params->get('disableanimatecss', 0))
{
	$doc->addStyleSheet(Uri::base(true) . '/components/com_sppagebuilder/assets/css/animate.min.css');
}

if (!$params->get('disablecss', 0))
{
	$doc->addStyleSheet(Uri::base(true) . '/components/com_sppagebuilder/assets/css/sppagebuilder.css');
}

HTMLHelper::_('script', 'components/com_sppagebuilder/assets/js/jquery.parallax.js');
HTMLHelper::_('script', 'components/com_sppagebuilder/assets/js/sppagebuilder.js', [], ['defer' => true]);

$menus = $app->getMenu();
$menu = $menus->getActive();
$menuClassPrefix = '';
$showPageHeading = 0;

// check active menu item
if ($menu)
{
	$menuClassPrefix 	= $menu->getParams()->get('pageclass_sfx');
	$showPageHeading 	= $menu->getParams()->get('show_page_heading');
	$menuheading 		= $menu->getParams()->get('page_heading');
}

$page = $this->item;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/builder/classes/addon.php';
$page->text = SpPageBuilderAddonHelper::__($page->text);
$content = json_decode($page->text);

// Add page css
if(isset($page->css) && $page->css)
{
	$doc->addStyledeclaration($page->css);
}

$app = Factory::getApplication();
$input = $app->input;
$Itemid = $input->get('Itemid', 0, 'INT');

$url = Route::_('index.php?option=com_sppagebuilder&view=form&layout=edit&tmpl=component&id=' . $page->id . '&Itemid=' . $Itemid);
$root = Uri::base();
$root = new Uri($root);
$link = $root->getScheme() . '://' . $root->getHost() . (!in_array($root->getPort(),array(80,443)) ? ':'.$root->getPort() : ''). $url;
?>

<div id="sp-page-builder" class="sp-page-builder <?php echo $menuClassPrefix; ?> page-<?php echo $page->id; ?>">

	<?php if($showPageHeading) : ?>
	<div class="page-header">
		<h1 itemprop="name">
			<?php
			if($menuheading) {
				echo $menuheading;
			} else {
				echo $page->title;
			}
			?>
		</h1>
	</div>
	<?php endif; ?>

	<div class="page-content">
		<?php $pageName = 'page-' . $page->id; ?>
		<?php echo AddonParser::viewAddons( $content, 0, $pageName ); ?>
		<?php
		$authorised = $user->authorise('core.edit', 'com_sppagebuilder') || $user->authorise('core.edit', 'com_sppagebuilder.page.' . $page->id) || ($user->authorise('core.edit.own', 'com_sppagebuilder.page.' . $page->id) && $page->created_by == $user->id);
		if ($authorised)
		{
			echo '<a class="sp-pagebuilder-page-edit" href="'. $link . '"><i class="fas fa-edit"></i> ' . Text::_('COM_SPPAGEBUILDER_PAGE_EDIT') . '</a>';
		}
		?>
	</div>
</div>
