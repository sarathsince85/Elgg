<?php

namespace Elgg\Discussions\Menus;

/**
 * Hook callbacks for menus
 *
 * @since 4.0
 * @internal
 */
class Filter {
	
	/**
	 * Add / remove tabs from the filter menu on the discussion pages
	 *
	 * @param \Elgg\Hook $hook 'filter_tabs', 'discussion'
	 *
	 * @return \Elgg\Menu\MenuItems
	 */
	public static function filterTabsForDiscussions(\Elgg\Hook $hook) {
		
		/* @var $items \ElggMenuItem[] */
		$items = $hook->getValue();
		
		// remove friends
		foreach ($items as $index => $item) {
			if ($item->getName() !== 'friend') {
				continue;
			}
			
			unset($items[$index]);
			break;
		}
		
		// add discussions in my groups
		$user = $hook->getUserParam();
		if ($user instanceof \ElggUser && elgg_is_active_plugin('groups')) {
			$selected = $hook->getParam('selected');
			
			$items[] = \ElggMenuItem::factory([
				'name' => 'my_groups',
				'text' => elgg_echo('collection:object:discussion:my_groups'),
				'href' => elgg_generate_url('collection:object:discussion:my_groups', [
					'username' => $user->username,
				]),
				'selected' => $selected === 'my_groups',
				'priority' => 400,
			]);
		}
		
		return $items;
	}
}