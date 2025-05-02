<template>
    <div class="side-nav__container flex">
        <div class="side-nav__img-wrap">
            <img class="side-nav__img" src="/icons/logo.png" alt="share">
        </div>

        <nav class="side-var-nav__menu">
            <div class="side-nav__list flex white">
                <div class="side-nav__home-img-wrap">
                    <img class="side-nav__img" src="/icons/home.png" alt="home">
                </div>
                <NuxtLink class="side-var-nav__link" to ="/">
                    ホーム
                </NuxtLink>
            </div>
            <form class="side-nav__logout-form flex" onsubmit="return confirm('ログアウトしますか？');">
                <div class="side-nav__logout-img-wrap">
                    <img class="side-nav__img" src="/icons/logout.png" alt="home">
                </div>
                <button class="side-nav__logout-button white" type="submit">
                    ログアウト
                </button>
            </form>
        </nav>

        <form class="post-form flex white">
            <h2 class="post-form__ttl">シェア</h2>
            <div class="post-form__input-group">
                <textarea class="post-form__textarea white" name="text" rows="8"></textarea>
                <div class="error-message">
                    message
                </div>
            </div>
            <button class="form-btn white post-form-btn" type="submit">シェアする</button>
        </form>
    </div>
</template>

<script setup>
import '~/assets/css/side_nav.css'
</script>