<?php
/*
 Plugin Name: WPSH User Meta View
 Plugin URI: http://www.nievs.com/?page_id=132
 Description: Plugin for displaying User Meta data
 Author: S. Hansen
 Version: 0.3
 Author URI: http://www.nievs.com
*/
?>

<?php
/*  Copyright 2010  Svein Hansen  (email : sveinh<at>gmail<dot>com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?php

/* WP callback to initate the plugin */
add_action('admin_menu', 'wpsh_usermetaview_create_menu');

/* Add the plugin to the 'Settings' menu */
function wpsh_usermetaview_create_menu() {

    add_options_page('WPSH UserMetaView',
            'WPSH UserMetaView',
            'manage_options',
            'WPSH UserMetaView',
            'wpsh_usermetaview_admin');
}


/* Utility function to produce a html-tag for the 'option' part of a html 'select' field.
   It produces 'option'-statements from 0 to $count, and marks the $selected element.
   $selected should be >= 0 and <= $count
*/
function wpsh_usermetaview_getSelectString($count, $selected) {
    $ret = '';
    for ( $counter = 0; $counter <= $count; $counter += 1) {
        $ret .= '<option ';
        if ($selected == $counter)
            $ret .= 'selected ';
        $ret .= 'value="';
        $ret .= $counter;
        $ret .= '">';
        if ($counter != 0)
            $ret .= $counter;
        $ret .= '</option> ';
    }
    return $ret;
}

/* Include the main page of the plugin */
function wpsh_usermetaview_admin() {

    global $wpdb;

    echo '<div class="wrap">' . "\n";
    echo '<h2>WPSH User Meta View</h2>' . "\n";

    if($_POST['wpsh_usermetaview_hidden'] == 'Y') {
        //Form data sent

        $num_metas = $_POST['wpsh_usermetaview_num_metas'];

        /* Parse the html fields to find all selected usermeta fields to use */
        $metas = array();
        for ( $counter = 1; $counter <= $num_metas; $counter += 1) {
            $lookup = 'wpsh_usermetaview_meta_' . $counter;
            $meta = $_POST[$lookup];
            $metaval = (int)$_POST[$meta];
            $metas[$meta] = $metaval;
        }

        /* Sort the array of fields to use according to the order choosen */
        asort($metas, ksort($metas));

        /* Find all users */
        $all_users = $wpdb->get_results("
            SELECT DISTINCT ID, user_nicename
            FROM $wpdb->users
            ORDER BY $wpdb->users.user_nicename
                ",
                OBJECT);

        $num_users = count($all_users);
        echo "Number of users: $num_users";

        /* Output table header */

        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead>';
        echo '<tr class="thead">' . "\n";
        echo '<th scope="col">ID</th>';
        echo '<th scope="col">User</th>';
        foreach ($metas as $k => $v) {
            if ($v != 0) {
                echo '<th scope="col">';
                echo $k;
                echo "</th>";
            }
        }
        echo "</tr>";
        echo '</thead>';

        echo '<tfoot>';
        echo '<tr class="thead">' . "\n";
        echo '<th scope="col">ID</th>';
        echo '<th scope="col">User</th>';
        foreach ($metas as $k => $v) {
            if ($v != 0) {
                echo '<th scope="col">';
                echo $k;
                echo "</th>";
            }
        }
        echo "</tr>";
        echo '</tfoot>';

        echo '<tbody>';

        /* Loop all users */
        foreach ($all_users as $user) {
            echo '<tr id="metalist-2" class="alternate">';
            echo "<td>$user->ID</td>";
            echo "<td>$user->user_nicename</td>";
            foreach ($metas as $k => $v) {

                if ($v != 0) {
                    /* Lookup specific value for user */
                    $q = '
                        SELECT meta_value
                        FROM ' . $wpdb->usermeta . '
                        WHERE user_id = ' . $user->ID . ' and meta_key = "' . $k . '"';
                    $metaval = $wpdb->get_row($q);
                    echo '<td>';
                    echo $metaval->meta_value;
                    echo '</td>';
                }
            }
            echo '</tr>';
        }
        echo '<tbody>';
        echo '</table>';

    } else {
        /* Normal page display */

        /* Find all possible meta_keys */
        $usermetas = $wpdb->get_results("
            SELECT DISTINCT meta_key
            FROM $wpdb->usermeta
            ORDER BY $wpdb->usermeta.meta_key",
                OBJECT
        );

        $num_metas = count($usermetas);

        echo '<form name="wpsh_usermetaview_form"' . "\n";
        echo '    method="post"' . "\n";
        echo '    action="';
        echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
        echo '">' . "\n";

        echo '    <input type="hidden" name="wpsh_usermetaview_hidden" value="Y">' . "\n";
        echo '    <input type="hidden" name="wpsh_usermetaview_num_metas" value="';
        echo $num_metas . '">' . "\n";

        /* Place all the meta_keys as hidden fields in the form */
        $count = 1;
        foreach ($usermetas as $usermeta) {
            echo '<input type="hidden"';
            echo 'name="wpsh_usermetaview_meta_' . $count . '"';
            echo 'value="' . $usermeta->meta_key . '">';
            echo "\n";
            $count += 1;
        }

        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead>';
        echo '  <tr class="thead">' . "\n";
        echo ' 	<th scope="col" class="manage-column">Meta field</th>';
        echo "\n";
        echo '  <th scope="col" class="manage-column">Include and order</th>';
        echo "\n";
        echo "  </tr>" . "\n";
        echo '</thead>';

        echo '<tfoot>';
        echo '  <tr class="thead">' . "\n";
        echo ' 	<th scope="col" class="manage-column">Meta field</th>';
        echo "\n";
        echo '  <th scope="col" class="manage-column">Include and order</th>';
        echo "\n";
        echo "  </tr>" . "\n";
        echo '</tr>';


        echo "  <tbody>" . "\n";

        /* Generate a table with meta_key name and dropdown select with sort orders */
        foreach ($usermetas as $usermeta) {
            echo '	<tr id="metalist-1" class="alternate">' . "\n";
            echo "  <td>" . $usermeta->meta_key . "</td>" . "\n";
            echo '  <td> <select name="' . $usermeta->meta_key . '">' . "\n";
            echo 		wpsh_usermetaview_getSelectString($num_metas, 0);
            echo "		</select> </td>" . "\n";
            echo " 	</tr>" . "\n";
        }

        echo "</tbody>" . "\n";
        echo "</table>" . "\n";

        echo '<p class="submit">' . "\n";
        echo '    <input type="submit" name="Submit" value="Create list" />' . "\n";
        echo "</p>" . "\n";
        echo "</form>" . "\n";
    }

    echo "</div>";
}

?>

