<?php
 
/**
 * ice-shards
 *
 * is.base.php	
 **/

abstract class IceShardBase {
	
	public function getClass( $element = NULL ) {
		if( NULL == $element ) {
			return get_class( $this );
		}
		return get_class( $element );
	}
	
	public function getClassName( $element = NULL ) {
		return $this->getClass( $element );
	}
	
	public function toString() {
		return $this->getClass();
	} 
	
}