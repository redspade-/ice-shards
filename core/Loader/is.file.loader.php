<?php

/**
 *	is.file.loader.php
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

	private $_hasLoaded = false;

	/**
	 *	Default constructor.
	 *
	 *	@access public
	 *	@param 	NULL|String|Object Module/file to be loaded, accepts empty parameter that could be later set using setFile mutator.
	 *	@see 	ISFileLoader::setFile()
	 **/
	public function __construct( $element = NULL ) {

		$this->setFile( $element );
		$this->setLoadingResult( $this->load() );

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
		 *		unfreeze( "Loader.ISFileLoader" ); 
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

			$length = count( $matches );

			/**
			 *	Resolve the possible "packages". Since the token following the namespace is the main class file, skip it. Remaining tokens
			 *	will be considered as "packages".
			 **/
			$packages = array();
			for( $i = 1; $i < $length; $i++ ) {
				$packages[] = $matches[ $i ];
			}

			/**
			 *	Initially I've implemented closure for this block, but I have to check if the version is greater than 5.3. Reading the codes
			 *	I've previously written, it falls to the same logic, and I need to add a version comparison to use closures, so I opted to remove
			 *	them and concluded to proceed with this approach.
			 **/			
			for( $i = 0; $i < $length; $i++ ) {
				$matches[ $i ] = strtolower( $matches[ $i ] );
			}

			$fileName = implode( '.', array( strtolower( self::DEFAULT_NAMESPACE ), implode( '.', $matches ), "php" ) );					
			$module = implode( DIRECTORY_SEPARATOR, $packages ) . DIRECTORY_SEPARATOR . $fileName;

			return $this->_load( $module );
		} else {
			/**
			 *	It is possible that the provided parameter is actually the file itself or the "canonical" one.
			 *	To distinguish between the two, check if the first token is the namespace "IS" and the last is "php, otherwise treat it as
			 *	"canonical" type.
			 */
			$tokens = explode( self::DEFAULT_PACKAGE_SEPARATOR, $file );			

			$length = count( $tokens );

			if( "php" == $tokens[ $length - 1 ] ) {
				/**
				 *	It's a file, it's a file!
				 *	Resolve the possible "packages". First token( not shifted ) is the namespace at index 0, main class at index 1, remainder 
				 *	considered packages, start at index 2 instead IF $length - 2 is at least 2.
				 **/				
				$packages = array();

				if( 2 == $length - 2 ) {
					for( $i = 2; $i < $length - 1; $i++ ) {
						$packages[] = ucfirst( $tokens[ $i ] );
					}
				}

				if( empty( $packages ) ) {
					/**
					 *	No packages found, use the file instead - must be sitting somewhere in the root of path directories.
					 */
					$module = $file;
				} else {
					$module = implode( DIRECTORY_SEPARATOR, $packages ) . DIRECTORY_SEPARATOR . $file;
				}				
			} else {
				/**
				 *	"Canonical" it is.
				 *	Resolve the possible "packages". First tokens are always considered "packages", last as the class name. Removing the last
				 *	token, we just need to glue the remaining items using the directory separator. What needs to be resolved is the given class
				 *	name.
				 **/
				$classFile = array_pop( $tokens );
				$matches = preg_split( "/([A-Z][a-z]+)/", $classFile, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

				if( empty( $matches ) ) {
					/**
					 *	No possible matches, not possible to get through here, but still - bail out!
					 **/
					throw new Exception( sprintf( "Unable to resolve class/file %s, verify that the class name is correctly spelled.", $classFile ) );
				}

				/**
				 *	Namespace should be the first item on the list.
				 */
				$baseNamespace = array_shift( $matches );

				if( self::DEFAULT_NAMESPACE != $baseNamespace ) {
					/**
					 *	Probably a different library or format, bail out as well!
					 **/
					throw new Exception( sprintf( "Unable to resolve class/file %s, ISFileLoader requires the namespace IS prepended.", $classFile ) );	
				}

				$length = count( $matches );
				for( $i = 0; $i < $length; $i++ ) {
					$matches[ $i ] = strtolower( $matches[ $i ] );
				}

				$fileName = implode( '.', array( strtolower( self::DEFAULT_NAMESPACE ), implode( '.', $matches ), "php" ) );					
				$module = implode( DIRECTORY_SEPARATOR, $tokens ) . DIRECTORY_SEPARATOR . $fileName;
			}
			return $this->_load( $module );
		}
		
	} // end member load

	/**
	 *	_load
	 *
	 *	@access private
	 *	@param 	String Target module to be loaded/included.
	 *	@return Boolean Returns true on successful file inclusion, false otherwise.
	 **/
	private function _load( $module ) {

		/**
		 *	load
		 *
		 *	@access public
		 *	@param 	NULL|String Specific element to be loaded instead of the stored property _file value.
		 *	@return Boolean Returns true on successful file inclusion, false otherwise.
		 **/
		$paths = explode( PATH_SEPARATOR, get_include_path() );
		foreach( $paths as $path ) {
			$file = $path . DIRECTORY_SEPARATOR . $module;
			if( is_file( $file ) ) {
				return require_once( $file );
			}
		}
		return false;

	} // end member _load

	/**
	 *	setLoadingResult
	 *
	 *	@access public
	 *	@param 	Boolean Flag to notify instance that loading has been successful.
	 *	@return Object Returns self to allow cascading calls.
	 **/
	public function setLoadingResult( $result ) {

		$this->_hasLoaded = is_bool( $result ) ? $result : false;
		return $this;

	} // end member setLoadingResult

	/**
	 *	hasLoaded
	 *
	 *	@access public
	 *	@return Boolean Returns true if the instance has successfully loaded the module, false otherwise.
	 **/
	public function hasLoaded() {

	} // end member hasLoaded

} // end class ISFileLoader

function unfreeze( $element ) {

	$loader = new ISFileLoader( $element );
	return $loader->hasLoaded();

} // end function unfreeze