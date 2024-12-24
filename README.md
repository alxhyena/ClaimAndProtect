# ClaimAndProtect Plugin 🏰

A powerful land claiming and `protection` plugin for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) that gives players complete control over their territory.

## ✨ Key Features

### 🛡️ Advanced Protection
- Full `protection` against griefing, building, and breaking
- Customizable permissions for every action
- Flag system to control PvP, explosions, and more
- `Protect` your builds, farms, and valuables

### 👥 Social Features 
- Add friends to your land with custom permissions
- Transfer `land` ownership
- Easy-to-use [Forms UI](https://github.com/jojoe77777/FormAPI) for management

### 💎 Economy Integration
- Flexible `pricing system`
- Supports [BedrockEconomy](https://github.com/cooldogepm/BedrockEconomy), [EconomyAPI](https://github.com/poggit-orphanage/EconomyS/tree/master/EconomyAPI), and XP
- Pay-per-block claiming
- `Refunds` on unclaiming

### 🎮 User ExperienceV
- Visual selection tools for claiming
- `Form-based` interface for all features
- Intuitive command system

### 🌍 Multi-Language
- Has language features that match the player's `minecraft language`
- support 2 `languages` ​​for now `(english, indonesian)`
- Message content and form content `can be changed` according to `language`
  
## 🎯 Perfect For
- Survival servers
- Towny servers
- RPG servers
- Creative servers
- Any server needing land protection

## 📝 Commands and Permissions
**Main Command: `/claimandprotect` or `/cnp`**

| Subcommand | Description | Permission | Default | Aliases |
|-|-|:-:|:-:|-|
| setfirst | Set the first position for claiming land | claimandprotect.command.setfirst | true | first, f |
| setsecond | Set the second position for claiming land | claimandprotect.command.setsecond | true | second, sec, s |
| claim | Claim your land after setup | claimandprotect.command.claim | true | - |
| settings | Settings your land | claimandprotect.command.settings | true | setting |
| here | Whose land is in this area? | claimandprotect.command.here | true | - |
| myland | View all owned land | claimandprotect.command.myland | true | - |
| info | Information of land with id | claimandprotect.command.infoland | true | - |
| remove | Remove/sell your land | claimandprotect.command.remove | true | sell, rm, delete |
| tp | Teleport to land with id | claimandprotect.command.tp | true | move, teleport, mv |
| admin | Admin feature | claimandprotect.command.admin | op | - |

### Admin Permissions
| Permission | Description | Default |
|-|-|:-:|
| claimandprotect.bypass | Bypass all protections | op |
| claimandprotect.admin.settings | Admin manage settings land | op |
| claimandprotect.admin.tp | Teleport to any land | op |
| claimandprotect.admin.myland | See any player's land | op |
| claimandprotect.admin.remove | Remove any player's land | op |

## ⚙️ Configuration
```yaml
# DO NOT EDIT THIS VALUE, INTERNAL USE ONLY.
config-version: 5

# Sets the prefix for this plugin.
prefix: "[&bClaim And Protect&r]"

# Sets the default language for the plugin, you can edit text and messages in this file.
default-language: en_US

# multi-economy setting
economy:
  # bedrockeconomy = BedrockEconomy
  # economys, economyapi = EconomyAPI
  # experience, exp, xp = Exp Minecraft
  provider: economyapi

# all settings of this plugin are here
cnp-settings:
  # players limit land
  # infinity for infinity limit
  player-limit-land: infinity
  # to maximize friends in your land
  # 'infinity' for infinite max friend
  max-friends: 5
  # price per block, ex 6 block 6 * price
  price-per-block: 100
  # Worlds to blacklist players to buy
  # ex, blacklist-world: ["lobby", "world"]
  blacklist-world: []
  # This is the percentage of the discount you get when selling land.
  # For example, if a player buys land for a price of '1000', if the value is filled in as '10'
  # it means he gets '900' because it is cut by 10%.
  percent-sold: 10
  # if 'true' will allow explosion on land
  # if 'false' then explosions are not allowed on land
  explosion: false

session-timer:
  first-session: false
    # If true, this will enable the first session timer.
  first-timer: 15 # Timer duration in seconds.

  second-session: false
    # If true, this will enable the second session timer.
  second-timer: 15 # Timer duration in seconds.

```

## 👨🏻‍💻 For Developer
### 🔧 Import LandManager Namespace 
```php
use xeonch\ClaimAndProtect\manager\LandManager;
```

### 🔍 Check if the Player is in a Land Area
the first parameter is the `position` in `pocketmine\world\Position;`
```php
$landManager = new LandManager();
if ($landManager->isInArea(Position)){
  // in area
} else {
  // not in area
```

### 📜 Check Data in Land Area
`Get the land data for the area the player` is in. The first parameter is the `position` from `pocketmine\world\Position;`.
```php
$landManager = new LandManager();
$landsInArea = $landManager->getLandsIn($position);

foreach ($landsInArea as $landId => $landData) {
  // Get Land ID
  echo $landId; // e.x: 4

  // Get Owner Name
  echo $landData['owner']; // e.x: ItsMeLordzy

  // Get World Name
  echo $landData['world']; // e.x: lobby

  // Get Position Coordinates
  echo $landData['pos']['first']; // e.x: 20, 55, 222, world
  echo $landData['pos']['second']; // e.x: 69, 55, 262, world

  // Get Land Price 💰
  echo $landData['price']; // e.x: 1200

  // Get Width of the Land 📏
  echo $landData['wide'] . " Block"; // e.x: 90 Block

  // Get Land Members 👥
  foreach ($landData['member'] as $member) {
    echo $member . "\n"; // e.x: Luqman
  }
}
```
### 👤 Check Data Using Owner Land
`Get all the land data` owned by the specified player.
```php
$name = 'ItsMeLordzy';
$landManager = new LandManager();
$lands = $landManager->getLandsByOwner($name);

if (empty($lands)) {
  echo "$name doesn't own any land yet";
  // Output: ItsMeLordzy doesn't own any land yet
}

foreach ($lands as $landId => $landData) {
  // Display all land IDs owned by the owner
  echo $landId . ", ";
  // e.x: 1, 4, 7
}

```
### 🆔 Check Data Using Land Id
Get the land data for a specific land `by ID`.
```php
$id = 1;
$landManager = new LandManager();
$landData = $landManager->getLand($id);

// Get Owner Name
echo $landData['owner']; // e.x: ItsMeLordzy
// ur code
```
### 🚶‍♂️ Example: Player Interaction with Land
An example where the player’s position is checked to see if they are within a land area. `Displays land ID and owner name` upon movement.
```php
use xeonch\ClaimAndProtect\manager\LandManager;
use pocketmine\event\player\PlayerMoveEvent;

    public function onPlayerMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $landManager = new LandManager();
        $landsInArea = $landManager->getLandsIn($player->getPosition());

        // Check if there is no land in the area 🛑
        if (empty($landsInArea)){
            $player->sendTip("There is no land here");
        }

        foreach ($landsInArea as $landId => $landData) {
            $player->sendTip("You are in land with ID: " . $landId . ", Owner: " . $landData['owner']);
            //output: You are in land with ID: 7, Owner: ItsMeLordzy
        }
    }
```
## 🏆 Contribution:
- **Bug Testing**: A special thanks to **LuqmanDv** for identifying and reporting critical issues that helped improve the stability and performance of this project.
- **Feedback**: Your invaluable suggestions played a key role in enhancing the project.

## 🚀 Thanks for Reading!
We appreciate your time spent reading this documentation. If you have any questions or suggestions, feel free to open an [issue](https://github.com/Jasson44/ClaimAndProtect/issues/new) or contribute to the repository.

Happy coding! 👨‍💻👩‍💻

## 📬 Contact Us

For further inquiries, please contact us at:

- Email: jassonmaulana447@gmail.com
- Discord Community: [Cl Community](https://discord.gg/Dt7hbgABSr) 
- WhatsApp: [Jasson](wa.me/62895366371823)
- Youtube: [XeonCh](https://www.youtube.com/@xeonch9888)
  
## 📝 License

This project is licensed under the `MIT License` - see the [LICENSE](LICENSE) file for details.
