<?php

/**
 * ice-shards
 *
 * is.loader.php
 **/

class IceShardLoader {
	
	const ICESHARD_NAMESPACE = "is";
	
	const SOURCE_SEPARATOR = '.';
		
	public static function load( $target = NULL ) {
		if( NULL == $target ) {
			return false;
		}
		
		if( is_string( $target ) ) {
			if( false === strpos( $target, self::SOURCE_SEPARATOR ) ) {
				return false;
			}
			
			$tokens = explode( self::SOURCE_SEPARATOR, $target );
			$file = "";
			if( is_array( $tokens ) ) {
				$file = self::ICESHARD_NAMESPACE .
						self::SOURCE_SEPARATOR .
						strtolower( array_pop( $tokens ) ) .
						'.php';												
			}
			if( empty( $file ) ) {
				return false;
			}			
			 
			$paths = explode( PATH_SEPARATOR, get_include_path() );
			if( empty( $paths ) ) {
				return false;
			}
			
			$tokens = implode( DIRECTORY_SEPARATOR, $tokens );
			$targetFile = $tokens . DIRECTORY_SEPARATOR . $file;
			foreach( $paths as $path ) {
				$requiredFile = $path . DIRECTORY_SEPARATOR . $targetFile;
				if( is_file( $requiredFile ) ) {
					return require_once( $requiredFile );
				}
			}
			
			return false;
		}
	}
	
}

if( !function_exists( "unfreeze" ) ) {
	function unfreeze( $file ) {
		return IceShardLoader::load( $file );
	}
}