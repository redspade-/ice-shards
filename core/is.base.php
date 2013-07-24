<?php

/**
 *	is.base.php
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

abstract class ISBase {

	const DEFAULT_NAMESPACE = "IS";
	
	/**
	 *	getClass
	 *
	 *	@access public
	 *	@param 	NULL|Object Target element to be identified.
	 *	@return String The class of the element provided.
	 *	@see 	get_class()
	 **/
	public function getClass( $element = NULL ) {

		if( is_null( $element ) ) {
			return get_class( $this );
		}
		return get_class( $element );

	} // end member getClass

	/**
	 *	getClassName
	 *
	 *	@access public
	 *	@param 	NULL|Object Target element to be identified.
	 *	@return String The class of the element provided.
	 *	@alias 	ISBase::getClass
	 **/
	public function getClassName( $element = NULL ) {

		return $this->getClass( $element );

	} // end member getClassName

	/**
	 *	__toString
	 *
	 *	@access public
	 *	@return String Default implementation returns the resolved class name.
	 *	@see 	ISBase::getClass
	 **/
	public function __toString() {

		return $this->getClass( $this );

	} // end member __toString

	/**
	 *	__toString
	 *
	 *	@access public
	 *	@return String Default implementation returns the resolved class name.
	 *	@see 	ISBase::getClass
	 **/
	public function toString() {

		return $this->getClass( $this );

	} // end member toString

	/**
	 *	getParent
	 *
	 *	@access public
	 *	@return String Returns the parent class of the current object.
	 *	@see 	get_parent_class()
	 **/
	public function getParent() {

		$parent = get_parent_class( $this );
		if( false === $parent ) {
			return $this->getClass( $this );
		}
		return $parent;

	} // end member getParent

} // end abstract class ISBase