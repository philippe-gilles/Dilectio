<?php

class view_component_notification extends view_component {
	public function panel($tab_notifications) {
		$o = "";
		foreach($tab_notifications as $notification) {
			/* Date de notification */
			$datetime_notif = strtotime($notification->date);
			$horodatage_notif = $this->format_horodatage($datetime_notif);

			/* Message de notification */
			$message_notif = lang_i18n::trad($this->langue, $notification->action);
			
			/* Lien */
			$href = "";
			$icone_type = "";
			$thread_id = (int) $notification->thread_id;
			if ($thread_id > 0) {
				$href = "thread-".$thread_id;
				$icone_type = "bubble";
			}
			else {
				$post_id = (int) $notification->post_id;
				if ($post_id > 0) {
					$post = db_post::get($post_id);
					if (!(is_null($post))) {
						$thread_id = $post->thread_id;
						$href = "thread-".$thread_id."_".$post_id;
						$type_id = (int) $post->type_id;
						if ($type_id > 0) {
							$type = db_type::get($type_id);
							if (!(is_null($type))) {
								$icone_type = $type->icon;
							}
						}
					}
				}
			}
			
			/* Icone */
			$icone_notification = "";
			if (strlen($icone_type) > 0) {
				$icone_notification = o::icomoon($icone_type, array("class" => "dilectio-notification-icone-type"));
			}

			/* Nom de la conversation */
			$thread_label = "";
			if ($thread_id > 0) {
				$thread = db_thread::get($thread_id);
				if (!(is_null($thread))) {
					$thread_label = $thread->label;
				}
			}

			/* Génération du html */
			$o .= o::div(_class, "dilectio-notification-event-wrapper");
			$o .= o::p();
			$o .= o::mdlicon("event", array("class" => "mdl-color-text--accent"));
			$o .= o::span_span($horodatage_notif, _class, "dilectio-notification-date");
			if (strlen($href) > 0) {
				$icone_view = o::mdlicon("visibility");
				$o .= o::a_a($icone_view, _href, $href, _class, "dilectio-notification-view");
			}
			$o .= o::_p();
			if (strlen($thread_label) > 0) {
				$o .= o::p_p($thread_label, _class, "dilectio-notification-thread_label");
			}
			$o .= o::p_p($icone_notification.$message_notif);
			$o .= o::_div();
		}
		return $o;
	}
}