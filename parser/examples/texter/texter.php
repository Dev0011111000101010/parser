<?php
	/****************************************************************************************************
	 * 
	 * This script demonstrates the Rtf text extraction capabilities of the RtfTexter classes 
	 * (RtfStringTexter or RtfFileTexter).
	 * 
	 ****************************************************************************************************/

	include('../../sources/RtfTexter.php');

	$texter	=  new RtfFileTexter ( 'sample.rtf' ) ;
	echo $texter -> AsString () ;