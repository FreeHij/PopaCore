<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
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

namespace pocketmine\entity;

use pocketmine\item\Item as ItemItem;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\network\Network;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use function mt_rand;

class Sheep extends Animal implements Colorable{
	const NETWORK_ID = 13;

	public $width = 0.9;
	public $height = 0.9;

	protected function initEntity(){
		parent::initEntity();
		$this->setMaxHealth(8);
		if(!isset($this->namedtag->Color) || !($this->namedtag->Color instanceof ByteTag)){
			$this->namedtag->Color = new ByteTag("Color", $this->getRandomColor());
		}
		$this->setDataProperty(Entity::DATA_AUX_VAL, Entity::DATA_TYPE_BYTE, $this->namedtag->Color->getValue() & 0xf);
	}

	public function getRandomColor(){
		$c = mt_rand(0,100);
		if($c <= 4){
			return 0xF;
		}
		if($c <= 9){
			return 0x7;
		}
		if($c <= 14){
			return 0x8;
		}
		if($c <= 17){
			return 0xC;
		}
		if(mt_rand(0, 500)){
			return 0x0;
		}
		return 0x6;
	}

	public function getName(){
		return "Sheep";
	}

	public function getDrops(){
		return [
			ItemItem::get(ItemItem::WOOL, $this->namedtag->Color->getValue(), 1)
		];
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk->setChannel(Network::CHANNEL_ENTITY_SPAWNING));

		parent::spawnTo($player);
	}
}
