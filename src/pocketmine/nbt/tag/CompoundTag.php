<?php

/*
 *
 * ____ _ _ __ __ _ __ __ ____
 * | _ \ ___ ___| | _____| |_| \/ (_)_ __ ___ | \/ | _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * | __/ (_) | (__| < __/ |_| | | | | | | | __/_____| | | | __/
 * |_| \___/ \___|_|\_\___|\__|_| |_|_|_| |_|\___| |_| |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */
namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use function get_class;

class CompoundTag extends NamedTag implements \ArrayAccess
{

	/**
	 *
	 * @param string $name
	 * @param NamedTag[] $value
	 */
	public function __construct($name = "", $value = [])
	{
		$this->name = $name;
		foreach ($value as $tag) {
			$this->{$tag->getName()} = $tag;
		}
	}

	public function offsetExists(mixed $offset): bool
	{
		return isset($this->{$offset});
	}

	public function offsetGet(mixed $offset): mixed
	{
		if (isset($this->{$offset}) and $this->{$offset} instanceof Tag) {
			if ($this->{$offset} instanceof \ArrayAccess) {
				return $this->{$offset};
			} else {
				return $this->{$offset}->getValue();
			}
		}

		return null;
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($value instanceof Tag) {
			$this->{$offset} = $value;
		} elseif (isset($this->{$offset}) and $this->{$offset} instanceof Tag) {
			$this->{$offset}->setValue($value);
		}
	}

	public function offsetUnset(mixed $offset): void
	{
		unset($this->{$offset});
	}

	public function getType()
	{
		return NBT::TAG_Compound;
	}

	public function read(NBT $nbt)
	{
		$this->value = [];
		do {
			$tag = $nbt->readTag();
			if ($tag instanceof NamedTag and $tag->getName() !== "") {
				$this->{$tag->getName()} = $tag;
			}
		} while (!($tag instanceof EndTag) and !$nbt->feof());
	}

	public function write(NBT $nbt)
	{
		foreach ($this as $tag) {
			if ($tag instanceof Tag and !($tag instanceof EndTag)) {
				$nbt->writeTag($tag);
			}
		}
		$nbt->writeTag(new EndTag());
	}

	public function __toString()
	{
		$str = get_class($this) . "{\n";
		foreach ($this as $tag) {
			if ($tag instanceof Tag) {
				$str .= get_class($tag) . ":" . $tag->__toString() . "\n";
			}
		}
		return $str . "}";
	}
}
