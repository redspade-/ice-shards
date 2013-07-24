<?php

/**
 *	is.loader.php
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

require_once( realpath( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . "is.loader.php" );

class ISFileLoader
	extends ISLoader {

	const DEFAULT_PACKAGE_SEPARATOR = '.';

	private $_file = NULL;

	/**
	 *	Default constructor.
	 *
	 *	@access public
	 *	@param 	NULL|String|Object Module/file to be loaded, accepts empty parameter that could be later set using setFile mutator.
	 *	@see 	ISFileLoader::setFile()
	 **/
	public function __construct( $element = NULL ) {

		$this->setFile( $element )->load();

	} // end constructor

	/**
	 *	setFile
	 *
	 *	@access public
	 *	@param 	NULL|String Module/file to be loaded, accepts empty parameter.
	 *	@return Object Returns self to allow cascading calls.
	 **/
	public function setFile( $file = NULL ) {

		/**
		 *	By convention, IceShards class files use a specific naming convention as follows:
		 *		is.<main-module-name>.<optional-package>.<optional-sub-packages1>.<optional-sub-packagesN>.php
		 *
		 *	where main-module-name is the main class that will be followed by the package that it belongs to( i.e. class ISFileLoader is a
		 *	concrete class for loading files that extends base class ISLoader, the former being "packaged" under "Loader" ) and the optional-
		 *	package is the non-mandatory "package" or "category" the class resides into. "Packages" could also be nested( i.e. inside the 
		 *	directory more subdirectories could be placed as well before the main module ) represented by optional-sub-packages above. Files use
		 *	the .php extension.
		 *
		 *	A utility function is provided along with this class, named "unfreeze". "unfreeze" is a global native PHP function that accepts a single
		 *	parameter - the module or file to be loaded - of type String. This String type argument could be the filename of the class to be loaded, 
		 *	the class that needs to be loaded( i.e. ISLoader, ISBase ) or "canonical" class inclusion. "Canonical" in this context means "a human-
		 *	readable sequence that basically follows the structure or the hierarchy of classes while explicitly pointing to the necessary resource".
		 *
		 *	These different types of parameters could be best illustrated with the following examples( using this class ISFileLoader ):
		 *
		 *	a) File inclusion by using the filename:
		 *		unfreeze( "is.file.loader.php" );
		 *	b) File inclusion by using the class name:
		 *		unfreeze( "ISFileLoader" ); 
		 *	c) File inclusion by using the "canonical" context:
		 *		unfreeze( "core.Loader.ISFileLoader" ); 
		 *	
		 */
		$this->_file = $file;
		return $this;

	} // end member setFile

	/**
	 *	getFile
	 *
	 *	@access public
	 *	@return NULL|String|Object Returns the value of the property _file.
	 **/
	public function getFile() {

		return $this->_file;

	} // end member getFile

	/**
	 *	load
	 *
	 *	@access public
	 *	@param 	NULL|String Specific element to be loaded instead of the stored property _file value.
	 *	@return Boolean Returns true on successful file inclusion, false otherwise.
	 **/
	public function load( $target = NULL ) {

		$file = empty( $target ) ? $this->getFile() : $target;
		if( empty( $file ) ) {
			throw new Exception( "Attempting to load empty file - must provide the class or file to be loaded." );
		}

		$usesSeparator = strpos( $file, self::DEFAULT_PACKAGE_SEPARATOR );

		if( false === $usesSeparator ) {
			/**
			 *	This must be the second case above, where the exact class name is used that needs to be resolved first.
			 */
			$matches = preg_split( "/([A-Z][a-z]+)/", $file, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
			if( empty( $matches ) ) {
				/**
				 *	No possible matches, not possible to get through here, but still - bail out!
				 **/
				throw new Exception( sprintf( "Unable to resolve class/file %s, verify that the class name is correctly spelled.", $file ) );
			}

			/**
			 *	Namespace should be the first item on the list.
			 */
			$baseNamespace = array_shift( $matches );

			if( self::DEFAULT_NAMESPACE != $baseNamespace ) {
				/**
				 *	Probably a different library or format, bail out as well!
				 **/
				throw new Exception( sprintf( "Unable to resolve class/file %s, ISFileLoader requires the namespace IS prepended.", $file ) );	
			}

			/**
			 *	Resolve the possible "packages".
			 **/
			

			/**
			 *	Initially I've implemented closure for this block, but I have to check if the version is greater than 5.3. Reading the codes
			 *	I've previously written, it falls to the same logic, and I need to add a version comparison to use closures, so I opted to remove
			 *	them and concluded to proceed with this approach.
			 **/
			$length = count( $matches );
			for( $i = 0; $i < $length; $i++ ) {
				$matches[ $i ] = strtolower( $matches[ $i ] );
			}

			$fileName = implode( '.', array( strtolower( self::DEFAULT_NAMESPACE ), implode( '.', $matches ), "php" ) );					

		}
		
	} // end member load

} // end class ISFileLoader