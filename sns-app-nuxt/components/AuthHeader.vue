<template>
    <header class="auth-header flex">
        <div class="auth-header__img-wrap">
            <img class="auth-header__img" src="/icons/logo.png" alt="share">
        </div>
        <nav class="auth-header-nav__menu">
            <ul class="auth-header-nav__list flex white">
                <li class="auth-header-nav__item">
                    <NuxtLink class="auth-header-nav__link" to ="/register">
                        新規登録
                    </NuxtLink>
                </li>
                <li class="auth-header-nav__item">
                    <NuxtLink class="auth-header-nav__link" to="/login">
                        ログイン
                    </NuxtLink>
                </li>
            </ul>
        </nav>
    </header>
</template>