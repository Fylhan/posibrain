Posibrain
========

A re-usable tchatbot PHP implementation to never be alone again. Hum, wait... What?
Nothing very intelligent at the moment, a set of predefined sentences lead to an other set of predefined answers. But still, synonyms can be defined, data from the sentence can be used in the answer, and of course the same answer won't be use every time.

*Posibrain*'s name is inspired by Asimov's positronic robots. Yes, as expected, R. Sammy is very silly ;-)

[![Build Status](https://travis-ci.org/Fylhan/posibrain.svg)](https://travis-ci.org/Fylhan/posibrain)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Fylhan/posibrain/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Fylhan/posibrain/?branch=master)

Installation & Usage
------------
Use Composer to download and install this library. To do so, create a folder and a file composer.json in it. Add this to the following file:

    "require": {
        "fylhan/posibrain": "dev-master",
    }
    
And install it using the command bellow:

    > php composer.php install
    
You can also download it, and also download all dependencies and paste them into a "vendor" folder.
No configuration is required because it is just an algorithm.

You can easily use it in your code:

    require '../vendor/autoload.php';
    use Posibrain\TchatBot;
    // OR without Composer autoload: include_once('src/Posibrain/TchatBot.php');

    header("Content-Type: text/html; charset=UTF-8");
    $bot = new TchatBot();
    $answer = $bot->generateAnswer('Bnmaster', 'Bonjour mon ami !', time());
    echo @$answer[1].' : '.$answer[0].'<br />';
    // May display for example: 'R. Sammy: Salutations noble ami.'


Or even using command line:

    Usage: php app/console submit "User message" ["User name"]
    > php app/console submit "Bonjour mon ami"
    R. Sammy: Salutations noble ami.
    
In command line you can also start an interactive tchat with the bot:

    Usage: php app/console discuss ["User name"]
    > php app/console discuss
    What is your name? (Anonymous) Fylhan
	You can start to discuss with the bot... Have a good tchat!
	
	Fylhan: Salut !
	R. Sammy: Tu vas bien ?
	
	Fylhan: Oui je vais bien ! Comment fait-on cuire tu riz ?
	R. Sammy: Moules à gaufres ! Demande à Wikipédia.
	
	Fylhan: Aurevoir
	R. Sammy: A bientôt ! Sale vilaine bête de tonnerre de Brest !
	
	See you soon!

Or you can list available bots and positrons (i.e. plugins) :

	> php app/console bots
	[0] R. Sammy alias Sammy: made the 11th of July 2013 by Fylhan
	[1] R. Sammy ISO: made the 30th of August 2013 by Fylhan
	> php app/console positrons
	[0] Haddock/HaddockPositron
	[1] Instinct/InstinctPositron
	
Or the REST API

	GET /api/bots
	GET /api/positrons
	GET /api/submit/bot-id/bot-lang?pseudo=Fylhan&msg=Qui est le président des Etats-Unis ?
	
How to test?
-----------
Open the file [dynamic.php](https://github.com/Fylhan/posibrain/blob/master/example/dynamic.php) in a browser to open an interactive tchat with R. Sammy, the crazy stupid bot.
At the moment, an old tchatbot version is also installed on the [Bnbox minitchat](http://la-bnbox.fr) (fr), you can try to speak with him in French by beginning your sentences by "@Hari": "@Hary Salut !".

For a static example, open the file [static.php](https://github.com/Fylhan/posibrain/blob/master/example/static.php) in a browser. A list of sentences, and their bot answer, will be displayed. Check the "logs" folder if you encounter some issues.

![Posibrain discussion example](https://raw.github.com/Fylhan/posibrain/master/doc/tchatbot-example.png)

You can also run unit tests using PHPUnit by running the "phpunit" command.

How to use and modify?
-----------
This documentation will be enhanced from time to time.

### Create a new Positron

A "Positron" is a plugin that will add intelligence and features to a bot.
It is a PHP class extending [\Posibrain\Positron\Positron](https://github.com/Fylhan/posibrain/blob/master/src/Posibrain/Positron/Positron.php). The file name and class name follow the pattern: src/Posibrain/Positron/XXX/XXXPositron.php. The class should be in the namespace \Posibrain\Positron\XXX.

To check an example, please have a look to the [Haddock Positron](https://github.com/Fylhan/posibrain/tree/master/src/Posibrain/Positron/Haddock) which adds 'Captain Haddock like insults' in the bot's answers.

A Positron may override several methods in order to select when to launch this positron, or to help other positrons to do their job, or to perform a new feature by itself.

* isPositronTriggered(TchatMessage $request)
* isBotTriggered(TchatMessage $request, $currentValue = true)
* analyseRequest(TchatMessage $request, AnalysedRequest $currentAnalysedRequest = null)
* isPositronStillTriggered(AnalysedRequest $request)
* isBotStillTriggered(AnalysedRequest $request, $currentValue = true)
* loadMemory(AnalysedRequest $request, $currentMemory = null)
* transformLoadedMemory(AnalysedRequest $request, $memory, $currentMemory = null)
* generateSymbolicAnswer(AnalysedRequest $request, $memory, TchatMessage $currentAnswer = null)
* provideMeaning(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
* beautifyAnswer(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
* updateMemory(AnalysedRequest $request, $memory, TchatMessage $answer)

Work in progress
----------------
A lot of things are in progress! I have created a first version of the bot algorithm, with a quick knowledge database. I am currently working on the package improvement (command line, API usability, brain selection, languages, plugin management). This is not the main goal of this project, but this is important to use it, and (eventually) contribute to it. Then... we will be able to increase this tchatbot intelligence :D

- [████100%] Add Composer support
- [████100%] Find a proper name -> *Posibrain*. Inspired by the "Robots" books of Isaac Asimov and his positronic robots.
- [████ 98%] Provide a way to select between several brains. [miss unit tests]
- [████ 98%] Manage several languages: ok. Only one fr brain is available. [miss unit tests]
- [██▒▒ 50%] Manage several charsets, not only UTF-8. Knowledge loading/storing should be good (to be checked), but bot reply charset is not done yet.
- [███▒ 75%] Check and improve folder structure. Currently: src/Posibrain, app/brains
- [███▒ 85%] Add command line support: discussion mode, submit one sentance, list bots and positrons. More to come! 
- [█▒▒▒ 25%] Plugin management (to modify a bot behaviour. E.g. search a link in a Shaarli...). A plugin is called a "Positron" and must be a class extending Posibrain\Positron\Positron and with a class name finished by "Positron".
- [▒▒▒▒ 0% ] Add REST API
- [▒▒▒▒ 0% ] Create an interactive tchat example
- [▒▒▒▒ 0% ] Add proper documentation
- [████100%] Add unit test engine: PHPUnit
- [█▒▒▒ 10%] Add more unit tests
- [█▒▒▒ 10%] Update the first brain's knowledge
- [█▒▒▒ 10%] Increase knowledge syntax possibilities (use Twig syntax instead if possible, add support for ${name, conceptorName, lang, birthday, userName} everywhere in responses or questions).
- [▒▒▒▒ 0% ] Add discussion log that will be used by the bot to learn and be a little bit more intelligent
- [▒▒▒▒ 0% ] Add more and more intelligence!


Licensing
--------
This piece of code is a free software under [LGPL v2.1+](http://choosealicense.com/licenses/lgpl-v2.1/). See [LICENSE file](https://github.com/Fylhan/tchatbot/blob/master/LICENSE) for more information. If this license is an issue for you, don't hesitate to contact me, I am very open on this :D

To summarize:

* Required
  * License and copyright notice
  * Disclose your modified Source if you re-distribute this software
* Permitted
  * Commercial Use
  * Modification
  * Distribution
  * Sub-licensing
  * Patent Grant
* Forbidden
  * Hold Liable
