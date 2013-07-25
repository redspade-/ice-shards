<?php

/**
 *	is.web.application.php
 *
 *	Copyright (C) 2013 Redspade Redspade
 *
 *	This library is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU Library General Public
 *	License as published by the Free Software Foundation; either
 *	version 2 of the License, or (at your option) any later version.
 *
 *	This library is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *	Library General Public License for more details.
 *
 *	You should have received a copy of the GNU Library General Public
 *	License along with this library; if not, write to the
 *	Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 *	Boston, MA  02110-1301, USA. 
 **/

require_once( realpath( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . "is.application.php" );
require_once( realpath( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . "is.configurable.php" );

class ISWebApplication
	extends ISApplication
		implements ISConfigurable {

	/**
	 *	$_instance
	 *
	 *	@access private
	 *	@static
	 *	@var 	Object Contains the single instance of this class.
	 **/
	private static $_instance = NULL;

	/**
	 *	getInstance
	 *
	 *	@access public
	 *	@static
	 *	@final
	 *	@return Object Returns instance of self.
	 **/
	public static final function getInstance() {

		if( NULL == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	} // end static member getInstance

	/**
	 *	Default constructor.
	 *
	 *	@access private
	 **/
	private function __construct() {

	} // end constructor

	/**
	 *	__clone
	 *
	 *	@access private
	 **/
	private function __clone() {

	} // end member __clone

	/**
	 *	configure
	 *
	 *	@access public
	 **/
	public function configure() {

	} // end member configure

	/**
	 *	getConfiguration
	 *
	 *	@access public
	 **/
	public function getConfiguration() {

	} // end member getConfiguration

} // end class ISWebApplication