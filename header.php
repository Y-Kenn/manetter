
<dev class="l-container__header">
    <div class="p-header">
        <div class="p-header__inner">
            <a href="index.php" class="p-header__logo"><img src="img/logo.png"></a>
            <div class="p-nav_trigger js-toggle_nav_trigger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <nav class="p-header__nav">
            <div class="p-header__nav__inner js-toggle_nav_target">
                <div class="p-user">
                    <div class="p-user__inner">
                        <div class="p-user__header">
                            <img src="<?php echo $my_info['data']['profile_image_url']; ?>" class="p-user__profile_img">
                            <span class="p-user__name"><?php echo $my_info['data']['name']; ?></span>
                            <span class="p-user__name"><?php echo '@' .$my_info['data']['username']; ?></span>
                        </div>
                        <div class="p-user__border">
                            <div class="p-user__num">
                                <div class="p-user__num__title">フォロワー</div>
                                <div class="p-user__num__data"><?php echo $my_info['data']['public_metrics']['followers_count']; ?></div>
                            </div>
                            <div class="p-user__num">
                                <span class="p-user__num__title">フォロー</span>
                                <span class="p-user__num__data"><?php echo $my_info['data']['public_metrics']['following_count']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="index.php" class="js-nav__home p-header__nav__item"><i class="fa-solid fa-house"></i><span class="u-margin--l--5">ホーム</span></a>
                <a href="likeSupport.php" class="js-nav__likeSupport p-header__nav__item"><i class="fa-solid fa-heart-circle-check"></i><span class="u-margin--l--5">いいねサポート</span></a>
                <a href="setting.php" class="js-nav__setting p-header__nav__item"><i class="fa-solid fa-gear"></i><span class="u-margin--l--5">設定/アカウント</span></a>
                <a href="logout.php" class="js-nav__logout p-header__nav__item"><i class="fa-solid fa-right-from-bracket"></i><span class="u-margin--l--5">ログアウト</span></a>
            </div>
        </nav>
    </div>
</dev> 
