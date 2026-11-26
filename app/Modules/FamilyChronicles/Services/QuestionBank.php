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
        ), self::BASE_QUESTIONS);

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
