Posibrain
========

A re-usable tchatbot PHP implementation to never be alone again. Hum, wait... What?
Nothing very intelligent at the moment, a set of predefined sentances lead to an other set of predefined answers. But still, synonyms can be defined, data from the sentance can be used in the answer, and of course the same answer won't be use everytime.

*Posibrain* name is inspired by Asimov's positronic robots. Yes, R. Sammy is currently stupid ;-)

Based on this [Eliza PHP tchatbot implementation](http://www.perkiset.org/forum/all_things_general_tech/artificial_intelligence_as_we_know_it_today-t1177.5.html;wap2=), at least for the concept.

Installation & Usage
------------
Use Composer to download and install this library. To do so, create a folder and a file composer.json in it. Add this to this file:

    "require": {
        "fylhan/posibrain": "0.*.*",
    }
    
And install it using the following command:

    > php composer.php install
    
You can also download it, and also download all dependencies and paste them into a "vendor" folder.
No configuration is required because it is just an algorithm.

You can easily use it in your code:

    require '../vendor/autoload.php';
    use Posibrain\TchatBot;
    // OR without Composer autoload: include_once('src/Posibrain/TchatBot.php');

    header("Content-Type: text/html; charset=UTF-8");
    $bot = new TchatBot();
    list($botName, $botMessage) = $bot->generateAnswer('Bnmaster', 'Bonjour mon ami', time());
    echo $botName.' : '.$botMessage.'<br />';
    // May display for example: 'R. Sammy: Salutations noble ami.'


Or even using command line:

    Usage: php app/console submit "User message" ["User name"]
    > php app/console submit "Bonjour mon ami"
    R. Sammy: Salutations noble ami.

How to test?
-----------
Launch the file [test.php](https://github.com/Fylhan/posibrain/blob/master/test/test.php) in a browser. A list of sentances, and their bot answer, will be displayed.

![Posibrain discussion example](https://raw.github.com/Fylhan/posibrain/master/doc/tchatbot-example.png)

An interactive tchat implementation will come. At the moment, this tchatbot is installed on the [Bnbox minitchat](http://la-bnbox.fr) (fr), you can try to speack with him in french by beginning your sentances by "@Hari".


How to use and modify?
-----------
A documentation will come.


Work in progress
----------------
A lot of things are in progress! I have created a first version of the bot algorithm, with a quick knowledge database. I am currently working on the package improvement (command line, API usability, brain selection, languages, plugin management). This is not the main goal of this project, but this is important to use it, and (eventually) contribute to it. Then... we will be able to increase this tchatbot intelligence :D

- [████100%] Add Composer support
- [████98%] Provide a way to select between several brains. [miss unit tests]
- [████98% ] Manage several langages (only fr translation is done currently). [miss unit tests]
- [██▒▒50% ] Manage several charsets, not only UTF-8. Knowledge loading/storing should be good (to be checked), but bot reply charset is not done yet. 
- [███▒75%] Check and improve folder structure/ Now: src/Posibrain
- [██▒▒50%] Add command line support (add options in "submit question" command, add command to list available bots)
- [▒▒▒▒ 0% ] Plugin management (to modify a bot behaviour. E.g. search a link in a Shaarli...). A plugin may be called a "Positron".
- [▒▒▒▒ 0% ] Remove lib dependencies. The idea is to provide a basic implementation that can be used if dependencies are not downloaded. Dependencies will become optional (and nice to have), and TchatBot will be light weight(er).
- [▒▒▒▒ 0% ] Create an interactive tchat example
- [████100% ] Find a proper name -> Posibrain. Inspired by the "Robots" books of Isaac Asimov and his positronic robots.
- [▒▒▒▒ 0% ] Add unit tests
- [▒▒▒▒ 0% ] Add proper documentation
- [█▒▒▒10% ] Update the first brain's knowledge
- [█▒▒▒10% ] Increase knowledge syntax possibilities (use Twig syntax instead if possible, add support for ${name, conceptorName, lang, birthday, userName} everywhere in responses or questions.
- [▒▒▒▒ 0% ] Add discussion log that will be used by the bot to learn and be a little bit more inteligent
- [▒▒▒▒ 0% ] Add more and more intelligence!


Licencing
--------
This piece of code is a free software under [LGPL v2.1](http://choosealicense.com/licenses/lgpl-v2.1/). See [LICENSE file](https://github.com/Fylhan/tchatbot/blob/master/LICENSE) for more information.

To summarize:
* Required
  * License and copyright notice
  * Disclose your modified Source if you re-distribute this software
* Permitted
  * Commercial Use
  * Modification
  * Distribution
  * Sublicensing
  * Patent Grant
* Forbidden 	
  * Hold Liable
