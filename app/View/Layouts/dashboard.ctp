<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$websiteTitle = 'Jukebox';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
        <?php echo $websiteTitle; ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

        echo $this->Html->css('jquery-ui.min');
		echo $this->Html->css('dashboard');

        echo $this->Html->script('vendor/jquery');
        echo $this->Html->script('vendor/jquery-ui');
        #echo $this->Html->script('vendor/iscroll');
		#echo $this->Html->script('vendor/jquery.easing.1.3');
        echo $this->Html->script('vendor/jquery.nicescroll.min');
        #echo $this->Html->script('vendor/jquery.nicescroll');
        #echo $this->Html->script('vendor/jquery.jplayer.min');
        echo $this->Html->script('vendor/underscore');
        echo $this->Html->script('vendor/backbone');
        echo $this->Html->script('vendor/backbone-fetch-cache');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
    <?php /*<script data-main="js/main.js" src="js/require.js"></script>*/ ?>
    <script>
        var app = {
            baseUrl: "<?php echo Router::url('/'); ?>",
            url: "<?php echo $appConfig['url'] ?>",
            holdingClipDir: "<?php echo $appConfig['app']['holdingClipDir'] ?>",
            holdingClipFilename: "<?php echo $appConfig['app']['holdingClipFilename'] ?>"
        };
    </script>
    <style>
        <?php if(date('m-d') == '10-30' || date('m-d') == '10-31'): ?>
        div.spinner {
            background-image: url('/Jukebox/img/164.GIF') !important;
        }
            <?php endif; ?>
    </style>
</head>
<body>
	<?php /*<div id="container">
		<div id="header">
			<h1><?php echo $this->Html->link($websiteTitle, '/'); ?></h1>
		</div>
		<div id="content">

			<?php echo $this->Session->flash(); */?>

			<?php echo $this->fetch('content'); ?>
		<?php /*</div>
	</div>
	<?php # echo $this->element('sql_dump'); */?>
</body>
</html>
