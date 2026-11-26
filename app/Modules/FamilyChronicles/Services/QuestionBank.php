<?php

declare(strict_types=1);

namespace App\Modules\FamilyChronicles\Services;

final class QuestionBank
{
    /** @var array<string, string> */
    private const CATEGORIES = [
        'roots' => 'Происхождение и родословная',
        'childhood' => 'Детство и дом',
        'family' => 'Родственники и семейные традиции',
        'education' => 'Школа и образование',
        'relationships' => 'Дружба, любовь и отношения',
        'work' => 'Работа и призвание',
        'daily-life' => 'Быт и повседневность',
        'food' => 'Еда и семейная кухня',
        'culture' => 'Культура, книги, музыка и кино',
        'society' => 'Общество и историческое время',
        'places' => 'Места и путешествия',
        'technology' => 'Техника и перемены',
        'turning-points' => 'Переломные моменты',
        'values' => 'Ценности и мировоззрение',
        'legacy' => 'Опыт и наследие',
    ];

    /** @var array<string, array{label:string, context:string, min_age:int|null, max_age:int|null}> */
    private const STAGES = [
        'early-childhood' => ['label' => 'Раннее детство', 'context' => 'в раннем детстве', 'min_age' => 0, 'max_age' => 5],
        'age-6-10' => ['label' => '6–10 лет', 'context' => 'в возрасте 6–10 лет', 'min_age' => 6, 'max_age' => 10],
        'age-11-14' => ['label' => '11–14 лет', 'context' => 'в возрасте 11–14 лет', 'min_age' => 11, 'max_age' => 14],
        'age-15-18' => ['label' => '15–18 лет', 'context' => 'в возрасте 15–18 лет', 'min_age' => 15, 'max_age' => 18],
        'age-19-25' => ['label' => '19–25 лет', 'context' => 'в возрасте 19–25 лет', 'min_age' => 19, 'max_age' => 25],
        'age-26-35' => ['label' => '26–35 лет', 'context' => 'в возрасте 26–35 лет', 'min_age' => 26, 'max_age' => 35],
        'age-36-45' => ['label' => '36–45 лет', 'context' => 'в возрасте 36–45 лет', 'min_age' => 36, 'max_age' => 45],
        'age-46-55' => ['label' => '46–55 лет', 'context' => 'в возрасте 46–55 лет', 'min_age' => 46, 'max_age' => 55],
        'age-56-65' => ['label' => '56–65 лет', 'context' => 'в возрасте 56–65 лет', 'min_age' => 56, 'max_age' => 65],
        'age-66-75' => ['label' => '66–75 лет', 'context' => 'в возрасте 66–75 лет', 'min_age' => 66, 'max_age' => 75],
        'age-76-plus' => ['label' => 'Старше 75 лет', 'context' => 'после 75 лет', 'min_age' => 76, 'max_age' => null],
        'timeless' => ['label' => 'Вне периода', 'context' => 'на протяжении жизни', 'min_age' => null, 'max_age' => null],
    ];

    /** @var list<array{0:string,1:string,2:string}> */
    private const BASE_QUESTIONS = [
        ['roots-family-name', 'roots', 'Что вам известно о происхождении вашей фамилии и семейных прозвищах?'],
        ['roots-oldest-story', 'roots', 'Какая самая старая семейная история дошла до вас и кто её рассказывал?'],
        ['roots-grandparents', 'roots', 'Что вы знаете о жизни своих бабушек и дедушек до вашего рождения?'],
        ['roots-lost-branches', 'roots', 'Есть ли родственники или ветви семьи, связь с которыми была утрачена?'],
        ['roots-family-objects', 'roots', 'Какие семейные вещи, фотографии или документы особенно важны и почему?'],
        ['childhood-first-memory', 'childhood', 'Какое ваше самое раннее воспоминание и какие детали в нём сохранились?'],
        ['childhood-home-sounds', 'childhood', 'Какие звуки, запахи и предметы лучше всего передают атмосферу дома вашего детства?'],
        ['childhood-safe-place', 'childhood', 'Где в детстве вы чувствовали себя наиболее спокойно и защищённо?'],
        ['childhood-games', 'childhood', 'Во что вы играли и какие правила придумывали сами?'],
        ['childhood-fears', 'childhood', 'Чего вы боялись в детстве и как взрослые относились к этим страхам?'],
        ['family-mother-day', 'family', 'Как выглядел обычный день вашей матери, когда вы были ребёнком?'],
        ['family-father-day', 'family', 'Как выглядел обычный день вашего отца или человека, заменявшего его?'],
        ['family-celebrations', 'family', 'Какие праздники и семейные ритуалы соблюдались особенно строго?'],
        ['family-conflicts', 'family', 'Как в семье обычно решали конфликты и о чём предпочитали молчать?'],
        ['family-humor', 'family', 'Какие семейные шутки, выражения или истории повторялись годами?'],
        ['education-teacher', 'education', 'Какой учитель сильнее всего повлиял на вас и чем именно?'],
        ['education-unwritten-rules', 'education', 'Какие неписаные правила существовали среди учеников вашего поколения?'],
        ['education-difficult-subject', 'education', 'Какой предмет давался труднее всего и чему это вас научило?'],
        ['education-self-study', 'education', 'Чему важному вы научились самостоятельно, вне официального образования?'],
        ['education-prestige', 'education', 'Какие профессии считались престижными во времена вашей молодости и почему?'],
        ['relationships-first-friend', 'relationships', 'Что вы помните о первом настоящем друге и почему вы сблизились?'],
        ['relationships-courtship', 'relationships', 'Как люди вашего поколения знакомились, ухаживали и начинали отношения?'],
        ['relationships-friendship-change', 'relationships', 'Какая дружба выдержала самые большие перемены в вашей жизни?'],
        ['relationships-boundaries', 'relationships', 'Какие поступки вы считаете непростительными в близких отношениях?'],
        ['relationships-kindness', 'relationships', 'Чью неожиданную доброту вы помните до сих пор?'],
        ['work-first-money', 'work', 'Как вы впервые заработали собственные деньги и на что их потратили?'],
        ['work-first-day', 'work', 'Что вас больше всего удивило в первый день серьёзной работы?'],
        ['work-mentor', 'work', 'Кого вы считаете своим профессиональным наставником?'],
        ['work-unseen-skill', 'work', 'Какое ваше умение редко замечали, хотя оно много раз помогало другим?'],
        ['work-choice', 'work', 'Если бы можно было заново выбрать профессию, что вы изменили бы?'],
        ['daily-household', 'daily-life', 'Какая домашняя работа раньше отнимала много времени, а сегодня почти исчезла?'],
        ['daily-shopping', 'daily-life', 'Как происходили обычные покупки и что считалось дефицитом?'],
        ['daily-neighbours', 'daily-life', 'Какими были отношения с соседями и в чём люди помогали друг другу?'],
        ['daily-clothes', 'daily-life', 'Как выбирали, покупали, шили и ремонтировали одежду?'],
        ['daily-weekend', 'daily-life', 'Как выглядел обычный выходной день вашей семьи?'],
        ['food-signature', 'food', 'Какое блюдо лучше всего представляет вашу семью и кто готовил его лучше всех?'],
        ['food-scarcity', 'food', 'Какие продукты было трудно достать и чем их заменяли?'],
        ['food-season', 'food', 'Как сезон влиял на питание и домашние заготовки?'],
        ['food-recipe', 'food', 'Какой семейный рецепт необходимо обязательно сохранить для потомков?'],
        ['food-disappeared', 'food', 'Какие блюда вашего детства сегодня почти исчезли?'],
        ['culture-book', 'culture', 'Какая книга изменила ваш взгляд на себя или окружающий мир?'],
        ['culture-music', 'culture', 'Какая музыка мгновенно возвращает вас в определённый период жизни?'],
        ['culture-cinema', 'culture', 'Какой поход в кино, театр или на концерт запомнился особенно ярко?'],
        ['culture-forbidden', 'culture', 'Какие книги, фильмы или песни считались спорными либо недоступными?'],
        ['culture-taste', 'culture', 'Как с возрастом изменился ваш художественный вкус?'],
        ['society-event', 'society', 'Какое историческое событие сильнее всего изменило вашу повседневную жизнь?'],
        ['society-news', 'society', 'Как люди узнавали новости и каким источникам доверяли?'],
        ['society-status', 'society', 'По каким признакам тогда судили о положении и успехе человека?'],
        ['society-freedom', 'society', 'В чём ваше поколение чувствовало больше свободы, а в чём — меньше?'],
        ['society-changed-norm', 'society', 'Какая общественная норма изменилась настолько, что молодым трудно её представить?'],
        ['places-home', 'places', 'Какое место вы называете домом, даже если давно там не живёте?'],
        ['places-route', 'places', 'Какой привычный маршрут вашей молодости вы могли бы пройти с закрытыми глазами?'],
        ['places-journey', 'places', 'Какое путешествие сильнее всего изменило ваше представление о мире?'],
        ['places-disappeared', 'places', 'Какое важное для вас место исчезло или изменилось до неузнаваемости?'],
        ['technology-first', 'technology', 'Какое техническое новшество впервые заставило вас почувствовать, что мир изменился?'],
        ['technology-adaptation', 'technology', 'К какой новой технологии вам было сложнее всего привыкнуть?'],
        ['technology-lost-skill', 'technology', 'Какое полезное умение исчезает из-за новых технологий?'],
        ['technology-communication', 'technology', 'Как технологии изменили ваши отношения с близкими?'],
        ['turning-choice', 'turning-points', 'Какое решение незаметно определило многие последующие годы вашей жизни?'],
        ['turning-failure', 'turning-points', 'Какая неудача позже оказалась полезной?'],
        ['turning-courage', 'turning-points', 'Когда вам пришлось действовать смелее, чем вы от себя ожидали?'],
        ['turning-loss', 'turning-points', 'Какая утрата изменила ваше понимание важного?'],
        ['turning-chance', 'turning-points', 'Какой случай или совпадение заметно изменили вашу судьбу?'],
        ['values-changed-belief', 'values', 'Какое убеждение, очевидное для вас в молодости, впоследствии изменилось?'],
        ['values-right', 'values', 'Как вы понимаете, что поступаете правильно, когда нет ясного ответа?'],
        ['values-faith', 'values', 'Как менялись ваши религиозные или философские взгляды?'],
        ['values-success', 'values', 'Что для вас сегодня означает успешная жизнь?'],
        ['values-regret', 'values', 'О чём вы сожалеете и чему это сожаление вас научило?'],
        ['legacy-advice', 'legacy', 'Какой совет вы хотели бы оставить человеку, который будет читать это через пятьдесят лет?'],
        ['legacy-misunderstood', 'legacy', 'Что потомки могут неправильно понять о времени, в котором вы жили?'],
        ['legacy-proud', 'legacy', 'Каким своим поступком вы гордитесь, даже если о нём мало кто знает?'],
        ['legacy-questions', 'legacy', 'Какие вопросы вы хотели бы задать собственным предкам?'],
        ['legacy-remember', 'legacy', 'Что о вас обязательно должно остаться в семейной памяти?'],
    ];

    /** @var list<array{0:string,1:string,2:string}> */
    private const FAMILY_TRADITION_QUESTIONS = [
        ['family-tradition-meaning', 'family', 'Что в вашей семье называли настоящей семейной традицией и почему это было важно?'],
        ['family-tradition-oldest', 'family', 'Какая традиция считается самой старой в вашей семье и до какого поколения можно проследить её историю?'],
        ['family-tradition-created', 'family', 'Какая традиция появилась уже при вашей жизни, кто её предложил и как она прижилась?'],
        ['family-tradition-changed', 'family', 'Какая семейная традиция заметно изменилась со временем и что именно стало другим?'],
        ['family-tradition-lost', 'family', 'Какая традиция исчезла из семейной жизни и почему её перестали соблюдать?'],
        ['family-tradition-keeper', 'family', 'Кто в семье лучше всех помнил правила праздников и следил, чтобы традиции соблюдались?'],
        ['family-tradition-disagreement', 'family', 'Бывали ли споры о том, как правильно соблюдать семейную традицию, и чем они заканчивались?'],
        ['family-tradition-younger-generation', 'family', 'Как младшие родственники относятся к старым традициям: продолжают, меняют или отвергают их?'],
        ['family-tradition-calendar', 'family', 'Какие даты семейного календаря нельзя было пропустить и как к ним готовились заранее?'],
        ['family-tradition-new-year-eve', 'family', 'Как проходил последний день года: кто что делал, что готовили и когда собирались вместе?'],
        ['family-tradition-new-year-morning', 'family', 'Что обычно происходило утром первого дня нового года в вашем доме?'],
        ['family-tradition-birthday', 'family', 'Как в семье отмечали дни рождения взрослых: кого звали, что дарили и какие слова говорили?'],
        ['family-tradition-child-birthday', 'family', 'Как выглядел детский день рождения, который вы помните лучше всего?'],
        ['family-tradition-anniversary', 'family', 'Какие семейные годовщины отмечали особенно торжественно и кто организовывал праздник?'],
        ['family-tradition-wedding', 'family', 'Какие свадебные обычаи соблюдали в семье и какой из них кажется вам самым необычным?'],
        ['family-tradition-newborn', 'family', 'Как семья встречала рождение ребёнка и кто первым приходил знакомиться с младенцем?'],
        ['family-tradition-first-school-day', 'family', 'Была ли особая традиция на первый школьный день: одежда, фотографии, подарки или семейный ужин?'],
        ['family-tradition-graduation', 'family', 'Как в семье отмечали окончание школы, университета или получение профессии?'],
        ['family-tradition-religious-holiday', 'family', 'Какие религиозные праздники отмечали дома и какие действия имели особый смысл?'],
        ['family-tradition-remembrance-day', 'family', 'Как в семье вспоминали умерших родственников и какие истории о них обязательно рассказывали?'],
        ['family-tradition-summer', 'family', 'Какая летняя традиция повторялась каждый год и с каким местом она была связана?'],
        ['family-tradition-winter', 'family', 'Какие зимние занятия или встречи были обязательной частью семейной жизни?'],
        ['family-tradition-weekend', 'family', 'Какой семейный ритуал был связан с выходными и кто чаще всего его начинал?'],
        ['family-tradition-sunday', 'family', 'Чем воскресенье отличалось от других дней недели в вашем доме?'],
        ['family-tradition-vacation', 'family', 'Как семья проводила отпуск и что старалась повторять в каждой поездке?'],
        ['family-tradition-return-home', 'family', 'Как встречали родственника после долгой поездки, службы, учёбы или работы вдали от дома?'],
        ['family-tradition-departure', 'family', 'Как провожали человека в дорогу и какие слова или действия считались обязательными?'],
        ['family-tradition-invited-guests', 'family', 'Как готовились к приходу гостей и какие роли получали разные члены семьи?'],
        ['family-tradition-unexpected-guests', 'family', 'Чем угощали неожиданного гостя и существовали ли особые правила гостеприимства?'],
        ['family-tradition-table-gathering', 'family', 'По каким поводам вся семья обязательно собиралась за одним столом?'],
        ['family-tradition-special-dish', 'family', 'Какое блюдо готовили только для определённого праздника и что происходило, если его не было на столе?'],
        ['family-tradition-recipe-person', 'family', 'Кто был хранителем главного семейного рецепта и как передавал его другим?'],
        ['family-tradition-cooking-together', 'family', 'Какое блюдо готовили всей семьёй и какую часть работы поручали вам?'],
        ['family-tradition-preserves', 'family', 'Какие сезонные заготовки делали вместе и как выглядел весь процесс от начала до конца?'],
        ['family-tradition-toast', 'family', 'Кто обычно произносил первый тост или поздравление и какие слова повторялись из года в год?'],
        ['family-tradition-seating', 'family', 'Были ли у членов семьи постоянные места за столом и что эти места говорили об отношениях в семье?'],
        ['family-tradition-table-rules', 'family', 'Какие правила поведения за столом считались обязательными и кто следил за ними?'],
        ['family-tradition-treat', 'family', 'Какое домашнее угощение всегда держали для детей, гостей или дороги?'],
        ['family-tradition-morning', 'family', 'Существовал ли общий утренний ритуал и какие звуки или запахи с ним связаны?'],
        ['family-tradition-evening', 'family', 'Как семья обычно завершала день и в какой момент все могли поговорить друг с другом?'],
        ['family-tradition-bedtime', 'family', 'Что взрослые делали перед сном детей: читали, пели, молились или рассказывали истории?'],
        ['family-tradition-housework', 'family', 'Какие домашние дела выполняли вместе и как распределяли обязанности?'],
        ['family-tradition-cleaning', 'family', 'Была ли генеральная уборка перед праздниками или сменой сезона и как она проходила?'],
        ['family-tradition-shopping', 'family', 'Какие покупки совершали всей семьёй и кто принимал окончательное решение?'],
        ['family-tradition-family-council', 'family', 'Как проходил семейный совет перед важным решением и чей голос имел особый вес?'],
        ['family-tradition-greeting', 'family', 'Как родственники приветствовали друг друга после разлуки: словами, жестами, объятиями или особым угощением?'],
        ['family-tradition-farewell', 'family', 'Какие слова обычно говорили при расставании и кто ввёл эту привычку?'],
        ['family-tradition-apology', 'family', 'Как в семье было принято мириться после ссоры и кто обычно делал первый шаг?'],
        ['family-tradition-support', 'family', 'Что семья обычно делала, когда кто-то заболевал, переживал утрату или сталкивался с трудностями?'],
        ['family-tradition-success', 'family', 'Как отмечали личный успех родственника — первую работу, победу, покупку или важное решение?'],
        ['family-tradition-storytelling', 'family', 'Какую семейную историю рассказывали почти на каждой встрече и менялась ли она в устах разных людей?'],
        ['family-tradition-song', 'family', 'Какие песни пели вместе и кто обычно начинал петь?'],
        ['family-tradition-game', 'family', 'В какую игру играли на семейных встречах и какие домашние правила отличали её от обычной версии?'],
        ['family-tradition-saying', 'family', 'Какая фраза, присказка или пословица стала семейной и в каких ситуациях её употребляли?'],
        ['family-tradition-language', 'family', 'Использовали ли дома особый язык, диалект или слова, непонятные посторонним?'],
        ['family-tradition-nicknames', 'family', 'Как появлялись семейные прозвища и какие из них передавались между поколениями?'],
        ['family-tradition-photo', 'family', 'Когда семья обязательно фотографировалась и кто обычно оставался за камерой?'],
        ['family-tradition-letters', 'family', 'Было ли принято хранить письма, открытки или записки и кто собрал семейный архив?'],
        ['family-tradition-heirloom', 'family', 'Какая вещь передаётся между поколениями, кому она принадлежала и по какому правилу выбирают следующего хранителя?'],
        ['family-tradition-gifts', 'family', 'Какие подарки считались особенно правильными или символичными в вашей семье?'],
        ['family-tradition-handmade', 'family', 'Что родственники делали своими руками специально для семьи и кто обучал этому навыку?'],
        ['family-tradition-clothes', 'family', 'Была ли одежда или украшение, которое надевали только по особым семейным случаям?'],
        ['family-tradition-place', 'family', 'В каком месте семья собиралась чаще всего и что обычно происходило во время этих встреч?'],
        ['family-tradition-neighbours', 'family', 'Какие праздники или обычаи семья разделяла с соседями и друзьями?'],
        ['family-tradition-distance', 'family', 'Как поддерживали семейные традиции родственники, жившие далеко друг от друга?'],
        ['family-tradition-migration', 'family', 'Какие обычаи удалось сохранить после переезда в другой город или страну, а какие пришлось изменить?'],
        ['family-tradition-values', 'family', 'Какую семейную ценность передавали не словами, а повторяющимися поступками и привычками?'],
        ['family-tradition-rules', 'family', 'Какое неписаное семейное правило знали все, хотя его никогда не обсуждали вслух?'],
        ['family-tradition-roles', 'family', 'Какие постоянные роли были у родственников на праздниках: организатор, повар, фотограф, рассказчик или миротворец?'],
        ['family-tradition-children', 'family', 'В каком возрасте детям впервые разрешали участвовать во взрослых семейных приготовлениях и что им поручали?'],
        ['family-tradition-elders', 'family', 'Как проявляли уважение к старшим родственникам в повседневной жизни и на праздниках?'],
        ['family-tradition-new-relative', 'family', 'Как знакомили с семейными обычаями нового супруга, друга или другого человека, вошедшего в семью?'],
        ['family-tradition-embarrassing', 'family', 'Какая семейная традиция в молодости вас смущала, а позже стала понятнее или дороже?'],
        ['family-tradition-funny-failure', 'family', 'Какой смешной провал во время праздника или семейного ритуала потом сам превратился в традиционную историю?'],
        ['family-tradition-senses', 'family', 'Какие запах, музыка, вкус или предмет сразу дают вам ощущение семейного праздника?'],
        ['family-tradition-one-day', 'family', 'Если подробно восстановить один традиционный семейный день от утра до ночи, что происходило по порядку?'],
        ['family-tradition-revive', 'family', 'Какую забытую традицию вы хотели бы возродить и кого нужно привлечь, чтобы это получилось?'],
        ['family-tradition-preserve', 'family', 'Какую традицию важно сохранить без изменений, а какую можно смело приспособить к современной жизни?'],
        ['family-tradition-invent', 'family', 'Какую новую традицию вы предложили бы семье начать уже в этом году?'],
    ];

    /** @var list<array{0:string,1:string,2:string}> */
    private const LONGITUDINAL_TRACKS = [
        ['home', 'daily-life', 'Каким был дом и ваше личное пространство %s?'],
        ['ordinary-day', 'daily-life', 'Как выглядел ваш обычный будний день %s?'],
        ['dinner', 'food', 'Что обычно было на ужин и кто собирался за столом %s?'],
        ['important-people', 'relationships', 'Кто был самым важным человеком для вас %s и почему?'],
        ['learning', 'education', 'Чему главному вы учились %s — намеренно или случайно?'],
        ['work-role', 'work', 'Какая работа или обязанность занимала вас больше всего %s?'],
        ['money', 'society', 'Как вы относились к деньгам и на что их чаще всего не хватало %s?'],
        ['culture', 'culture', 'Какие книги, музыка, фильмы или передачи сопровождали вас %s?'],
        ['technology', 'technology', 'Какими устройствами и средствами связи вы пользовались %s?'],
        ['dreams', 'values', 'О чём вы больше всего мечтали %s?'],
    ];

    /** @return array<string, string> */
    public function categories(): array
    {
        return self::CATEGORIES;
    }

    /** @return array<string, array{label:string, context:string, min_age:int|null, max_age:int|null}> */
    public function stages(): array
    {
        return self::STAGES;
    }

    /** @return list<array{key:string, category:string, category_label:string, stage:string, stage_label:string, text:string}> */
    public function all(): array
    {
        $questions = array_map(fn (array $question): array => $this->question(
            $question[0],
            $question[1],
            'timeless',
            $question[2],
        ), [...self::BASE_QUESTIONS, ...self::FAMILY_TRADITION_QUESTIONS]);

        foreach (self::LONGITUDINAL_TRACKS as [$track, $category, $template]) {
            foreach (self::STAGES as $stage => $definition) {
                if ($stage === 'timeless') {
                    continue;
                }

                $questions[] = $this->question(
                    "timeline-{$track}-{$stage}",
                    $category,
                    $stage,
                    sprintf($template, $definition['context']),
                );
            }
        }

        return $questions;
    }

    /** @return list<array{key:string, category:string, category_label:string, stage:string, stage_label:string, text:string}> */
    public function availableFor(?int $birthYear): array
    {
        if ($birthYear === null) {
            return $this->all();
        }

        $age = max(0, (int) now()->format('Y') - $birthYear);

        return array_values(array_filter($this->all(), static function (array $question) use ($age): bool {
            $stage = self::STAGES[$question['stage']];

            return $stage['min_age'] === null || $stage['min_age'] <= $age;
        }));
    }

    /** @return array{key:string, category:string, category_label:string, stage:string, stage_label:string, text:string}|null */
    public function find(string $key): ?array
    {
        foreach ($this->all() as $question) {
            if (hash_equals($question['key'], $key)) {
                return $question;
            }
        }

        return null;
    }

    /** @return array{key:string, category:string, category_label:string, stage:string, stage_label:string, text:string} */
    private function question(string $key, string $category, string $stage, string $text): array
    {
        return [
            'key' => $key,
            'category' => $category,
            'category_label' => self::CATEGORIES[$category],
            'stage' => $stage,
            'stage_label' => self::STAGES[$stage]['label'],
            'text' => $text,
        ];
    }
}
