<?php
/**
 * A Site entity.
 *
 * \ElggSite represents a single site entity.
 *
 * An \ElggSite object is an \ElggEntity child class with the subtype
 * of "site."  It is created upon installation and holds information about a site:
 *  - name
 *  - description
 *  - url
 *
 * Every \ElggEntity belongs to a site.
 *
 * @note Internal: \ElggSite represents a single row from the entities table.
 *
 * @link       http://learn.elgg.org/en/stable/design/database.html
 *
 * @property      string $name        The name or title of the website
 * @property      string $description A motto, mission statement, or description of the website
 * @property-read string $url         The root web address for the site, including trailing slash
 */
class ElggSite extends \ElggEntity {

	/**
	 * {@inheritdoc}
	 */
	public function getType() {
		return 'site';
	}

	/**
	 * {@inheritdoc}
	 */
	public function save() {
		$db = $this->getDatabase();
		$row = $db->getDataRow("
			SELECT guid FROM {$db->prefix}entities WHERE type = '{$this->getType()}'
		");
		if ($row) {
			if ($row->guid == $this->attributes['guid']) {
				// can save active site
				return parent::save();
			}

			_elgg_services()->logger->error('More than 1 site entity cannot be created.');
			return false;
		}

		return parent::save(); // TODO: Change the autogenerated stub
	}

	/**
	 * Delete the site.
	 *
	 * @note You cannot delete the current site.
	 *
	 * @return bool
	 * @throws SecurityException
	 */
	public function delete() {
		if ($this->guid == 1) {
			throw new \SecurityException('You cannot delete the current site');
		}

		return parent::delete();
	}

	/**
	 * Disable the site
	 *
	 * @note You cannot disable the current site.
	 *
	 * @param string $reason    Optional reason for disabling
	 * @param bool   $recursive Recursively disable all contained entities?
	 *
	 * @return bool
	 * @throws SecurityException
	 */
	public function disable($reason = "", $recursive = true) {
		if ($this->guid == 1) {
			throw new \SecurityException('You cannot disable the current site');
		}

		return parent::disable($reason, $recursive);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __set($name, $value) {
		if ($name === 'url') {
			_elgg_services()->logger->warn("ElggSite::url cannot be set");
			return;
		}
		parent::__set($name, $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __get($name) {
		if ($name === 'url') {
			return $this->getURL();
		}
		return parent::__get($name);
	}

	/**
	 * Returns the URL for this site
	 *
	 * @return string The URL
	 */
	public function getURL() {
		return _elgg_config()->wwwroot;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepareObject($object) {
		$object = parent::prepareObject($object);
		$object->name = $this->getDisplayName();
		$object->description = $this->description;
		unset($object->read_access);
		return $object;
	}

	/**
	 * Get the domain for this site
	 *
	 * @return string
	 * @since 1.9
	 */
	public function getDomain() {
		$breakdown = parse_url($this->url);
		return $breakdown['host'];
	}

	/**
	 * Get the email address for the site
	 *
	 * This can be set in the basic site settings or fallback to noreply@domain
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function getEmailAddress() {
		$email = $this->email;
		if (empty($email)) {
			$email = "noreply@{$this->getDomain()}";
		}

		return $email;
	}
}
