<?php

/**
 * ice-shards
 *
 * is.appbase.php
 **/

require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'is.loader.php' );

unfreeze( "Base" );

abstract class IceShardAppBase
	extends IceShardBase {
	
}