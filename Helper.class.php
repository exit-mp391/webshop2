<?php

class Helper {

  public static function session_start() {
    if ( !isset($_SESSION) ) {
      session_start();
    }
  }

  public static function session_destroy() {
    Helper::session_start();
    $_SESSION = [];
    session_destroy();
  }

  public static function success($message, $title = "Success!") {
		echo '<div class="alert alert-success">';
		echo '<b>' . $title . ' </b>';
		echo $message;
		echo '</div>';
	}

	public static function error($message, $title = "Error!") {
		echo '<div class="alert alert-danger">';
		echo '<b>' . $title . ' </b>';
		if ( is_array($message) ) {
			echo "<br />";
			foreach( $message as $msg ) {
				echo $msg . '<br />';
			}
		} else {
			echo $message;
		}
		echo '</div>';
	}

	public static function warn($message, $title = "Warning!") {
		echo '<div class="alert alert-warning">';
		echo '<b>' . $title . ' </b>';
		echo $message;
		echo '</div>';
	}

	// public static function warn($message, $title = "Warning!") {
	// 	echo "
	// 		<div class=\"alert alert-warning\">
	// 			<b>$title</b>
	// 			$message
	// 		</div>
	// 	";
	// }

}
