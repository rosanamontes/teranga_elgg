<?php
/**
 * Save a discussion reply
 *
* 	Plugin: myTripsTeranga from previous version of @package ElggGroup
*	Author: Rosana Montes Soldado 
*			Universidad de Granada
*	Licence: 	CC-ByNCSA
*	Reference:	Microproyecto CEI BioTIC Ref. 11-2015
* 	Project coordinator: @rosanamontes
*	Website: http://lsi.ugr.es/rosana
* 	Project colaborator: Antonio Moles 
*	
*   Project Derivative:
*	TFG: Desarrollo de un sistema de gestión de paquetería para Teranga Go
*   Advisor: Rosana Montes
*   Student: Ricardo Luzón Fernández
* 
*/

// Get input
$topic_guid = (int) get_input('topic_guid');
$text = get_input('description');
$reply_guid = (int) get_input('guid');

// reply cannot be empty
if (empty($text)) {
	register_error(elgg_echo('trippost:nopost'));
	forward(REFERER);
}

if ($topic_guid) {
	$topic = get_entity($topic_guid);
	if (!elgg_instanceof($topic, 'object', 'tripforumtopic')) {
		register_error(elgg_echo('trippost:nopost'));
		forward(REFERER);
	}

	$trip = $topic->getContainerEntity();
	if (!$trip->canWriteToContainer()) {
		register_error(elgg_echo('myTrips:notmember'));
		forward(REFERER);
	}
}

$user = elgg_get_logged_in_user_entity();
if ($reply_guid) {
	$reply = get_entity($reply_guid);

	if (!elgg_instanceof($reply, 'object', 'discussion_reply', 'ElggDiscussionReply')) {
		register_error(elgg_echo('discussion:reply:error:notfound'));
		forward(REFERER);
	}

	if (!$reply->canEdit()) {
		register_error(elgg_echo('myTrips:notowner'));
		forward(REFERER);
	}

	$reply->description = $text;

	if ($reply->save()) {
		system_message(elgg_echo('myTrips:forumpost:edited'));
	} else {
		register_error(elgg_echo('myTrips:forumpost:error'));
	}
} else {
	// add the reply to the forum topic
	$reply = new ElggDiscussionReply();
	$reply->description = $text;
	$reply->access_id = $topic->access_id;
	$reply->container_guid = $topic->getGUID();
	$reply->owner_guid = $user->getGUID();

	$reply_guid = $reply->save();

	if ($reply_guid == false) {
		register_error(elgg_echo('myTripspost:failure'));
		forward(REFERER);
	}

	elgg_create_river_item(array(
		'view' => 'river/object/discussion_reply/create',
		'action_type' => 'reply',
		'subject_guid' => $user->guid,
		'object_guid' => $reply->guid,
		'target_guid' => $topic->guid,
	));

	system_message(elgg_echo('myTripspost:success'));
}

forward(REFERER);