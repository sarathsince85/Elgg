<?php
/**
 * Elgg Message board: delete message action
 *
 * @package ElggMessageBoard
 */

elgg_deprecated_notice("The action 'messageboard/delete' is deprecated, use 'annotation/delete'", '3.3');

$annotation_id = (int) get_input('annotation_id');
$message = elgg_get_annotation_from_id($annotation_id);
$ok_output = ['deleted' => $message->toObject()];
if ($message && $message->canEdit() && $message->delete()) {
	return elgg_ok_response($ok_output, elgg_echo('messageboard:deleted'));
}

return elgg_error_response(elgg_echo('messageboard:notdeleted'));
