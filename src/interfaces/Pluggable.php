<?php
/* =====================================================================
 * Atk4-wp => An Agile Toolkit PHP framework interface for WordPress.
 *
 * This interface enable the use of the Agile Toolkit framework within a WordPress site.
 *
 * Please note that atk or atk4 mentioned in comments refer to Agile Toolkit or Agile Toolkit version 4.
 * More information on Agile Toolkit: http://www.agiletoolkit.org
 *
 * Author: Alain Belair
 * Licensed under MIT
 * =====================================================================*/
/**
 * Interface for creating Plugin using atk.
 */

namespace atkwp\interfaces;


interface Pluggable
{
	public function __construct($name, $path);
	public function activatePlugin();
	public function deactivatePlugin();
	public function uninstallPlugin();
}