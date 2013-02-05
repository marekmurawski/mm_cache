<?php

/**
 * Plugin mmCache Controller
 *
 * Inspired by AS Cache by Gilles Doge and Kohana Framework
 * Fragment helper
 *
 * @author Marek Murawski <http://www.marekmurawski.pl>
 * @author Gilles Doge <gde@antistatique.net>, Antistatique.net
 *
 */
class MmCacheController extends PluginController {

	public function __construct() {
		AuthUser::load();
		if (!(AuthUser::isLoggedIn())) {
			redirect(get_url('login'));
		}

		if (!AuthUser::hasPermission('admin_view')) {
			redirect(URL_PUBLIC);
		}

		$this->setLayout('backend');
		$this->assignToLayout('sidebar', new View('../../plugins/mm_cache/views/sidebar'));
	}

	public function index() {
		$rootDir = CMS_ROOT . DS . Plugin::getSetting('dir', 'mm_cache');
		$extension = Plugin::getSetting('extension', 'mm_cache');
		clearstatcache();
		$bytesTotalValid = 0;
		$bytesTotalExpired = 0;
		$validFilesCount = 0;
		$expiredFilesCount = 0;
		$tnow = time();
		$cacheFiles = array();
		$iterator = new RecursiveDirectoryIterator($rootDir);
		foreach (new RecursiveIteratorIterator($iterator) as $filename => $cur) {
			if (!endsWith($filename, '.htaccess')&& !endsWith($filename, '.')) {
				$age = intval($tnow - $cur->getMTime());
				$fname = str_replace($rootDir, '', $filename);
				$fsize = $cur->getSize();

				$keyname = substr($fname, 1, -strlen($extension) - 1); // -1 for dot
				$timeout = MmCache::getInstance()->getTimeout($keyname);
				$ttl = intval($timeout - $tnow);

				if ($timeout > 0) {
					$bytesTotalValid+=$fsize;
					$validFilesCount++;
					$lifetime = intval($age + $ttl);
				} else {
					$bytesTotalExpired+=$fsize;
					$expiredFilesCount++;
					$lifetime = 'expired';
				}

				$cacheFiles[] = array(
						'valid' => ($timeout > 0),
						'name' => $fname,
						'fullname' => $filename,
						'size' => $fsize,
						'updated' => $cur->getMTime(),
						'age' => $age,
						'lifetime' => $lifetime,
				);
			}
		} // foreach

		$bytesTotalValid = number_format($bytesTotalValid);
		$bytesTotalExpired = number_format($bytesTotalExpired);


		$this->display('mm_cache/views/index', array(
				'cacheFiles' => $cacheFiles,
				'bytesTotalValid' => $bytesTotalValid,
				'bytesTotalExpired' => $bytesTotalExpired,
				'validFilesCount' => $validFilesCount,
				'expiredFilesCount' => $expiredFilesCount,
		  )
		);
	}

	/**
	 * Action to clear all cache entries
	 * */
	public function clearcacheall() {
		if (!MmCache::getInstance()->clean('all')) {
			Flash::set('error', __('All cache entries have NOT been cleared!'));
		} else {
			Flash::set('success', __('All cache entries have been cleared!'));
		}

		redirect(get_url('plugin/mm_cache'));
	}

	/**
	 * Action to clear expired cache entries
	 * */
	public function clearcacheold() {
		if (!MmCache::getInstance()->clean('old')) {
			Flash::set('error', __('Expired cache entries have NOT been cleared!'));
		} else {
			Flash::set('success', __('Expired cache entries have been cleared!'));
		}
		redirect(get_url('plugin/mm_cache'));
	}

	/**
	 * Action to clear cache entries by searching substring in filename
	 * */
	public function clearcachebyname() {
		(isset($_POST['name'])) ? $name = $_POST['name'] : $name = false;
		if ($name) {
			$count = MmCache::getInstance()->cleanByName($name);
			if ($count) {
				Flash::set('success', __('Cleared :1 cache entries!', array(':1' => $count)));
			} else {
				Flash::set('error', __('Cache entries with string ":1" NOT found!', array(':1' => $name)));
				Flash::set('mmcachesearchname', $name);
			}
		} else {
			Flash::set('error', __('Please provide a search string to delete cache entries!'));
			Flash::set('mmcachesearchname', $name);
		}
		redirect(get_url('plugin/mm_cache'));
	}

	/**
	 * Settings for mmCache to change specific features
	 */
	public function settings() {
		$settings = Plugin::getAllSettings('mm_cache');
		if ($_POST) {
			$settings = $_POST;
			$settings['dir'] = trim($settings['dir'], '/\\'); // trim slashes from start and end of dir
			$settings['default_lifetime'] = intval($settings['default_lifetime']);
			$settings['extension'] = ltrim($settings['extension'], '.');
			if (Plugin::setAllSettings($settings, 'mm_cache')) {
				Flash::set('success', __('mmCache settings saved') . print_r($settings, true));
			} else {
				Flash::set('error', __('Error saving settings'));
			}
			redirect(get_url('plugin/mm_cache/settings'));
		}
		$this->display('mm_cache/views/settings', $settings);
	}

	/**
	 * Settings for mmCache to change specific features
	 */
	public function documentation() {
		$this->display('mm_cache/views/documentation');
	}

	private function _secureDirectory() {
		try {
			if (!is_dir(CMS_ROOT . DS . $settings['dir'])) {
				mkdir(CMS_ROOT . DS . $settings['dir']);
			};
			file_put_contents(CMS_ROOT . DS . $settings['dir'] . DS . '.htaccess', 'DENY FROM ALL');
			Flash::set('info', __('Successfully created :dir directory', array(':dir'=>CMS_ROOT . DS . $settings['dir'])));
		} catch (Exception $e) {
			Flash::set('error', 'Error while creating directory and/or .htaccess file!' . '<br/>' . $e->getMessage());
		}
	}

}

// END public class CacheController