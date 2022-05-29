<?php

declare(strict_types=1);

namespace alvin0319\SizeShop\form;

use alvin0319\SizeShop\SizeShop;
use onebone\economyapi\EconomyAPI;
use OnixUtils\OnixUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_bool;

class SizeBuyForm implements Form{
	/** @var string */
	protected string $type;

	public const MONEY = [
		SizeShop::SIZE_BIG => 5000000,
		SizeShop::SIZE_SMALL => 3000000
	];

	public function __construct(string $type){
		$this->type = $type;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "modal",
			"title" => "크기 " . SizeShop::convert($this->type),
			"content" => "정말 크기 " . SizeShop::convert($this->type) . "(을)를 구매하시겠습니까?\n\n가격: " . EconomyAPI::getInstance()->koreanWonFormat(self::MONEY[$this->type]),
			"button1" => "§l* 네",
			"button2" => "§l* 아니요"
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_bool($data)){
			return;
		}
		if($data){
			if(EconomyAPI::getInstance()->myMoney($player) < self::MONEY[$this->type]){
				OnixUtils::message($player, "구매에 필요한 돈이 부족합니다.");
				return;
			}
			if(!SizeShop::getInstance()->canAddSize($player, $this->type)){
				OnixUtils::message($player, "이미 최대 크기에 도달했습니다.");
				return;
			}
			EconomyAPI::getInstance()->reduceMoney($player, self::MONEY[$this->type]);
			SizeShop::getInstance()->addSize($player, $this->type, 0.1);
			OnixUtils::message($player, "구매에 성공했습니다.");
		}
	}
}