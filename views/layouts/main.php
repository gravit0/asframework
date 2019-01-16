<!DOCTYPE html>
<html>
<head>
    <?php visual::renderHead() ?>
</head>
<body>
<div id="bodydiv">
    <div id="nav-panel">
        <?php

        use \widgets\ActiveMenu;

        $a = new ActiveMenu;
        $a->add('Главная', '?r=index', 'index');
        $a->add('Профиль', '?r=index&a=user', 'test');
        if (!app::$user || !app::$user->isAuth) {
            $a->add('Вход', '?r=index&a=auth', 'index_auth');
            $a->add('Регистрация', '?r=index&a=reg', 'index_reg');
        } else
            $a->add('Выход(' . app::$user->login . ')', '?r=index&a=exit', 'index_exit');
        $a->setActive(visual::$activeid, true);
        $a->printHtml();
        ?>
    </div>
    <div id="content">
        <?php visual::renderBody() ?>
    </div>
</div>
</body>
</html>
