<template>
    <AuthHeader></AuthHeader>
    <main>
        <div class="form-container radius5 flex">
            <h1 class="form-header">新規登録</h1>
            <form class="form-group flex">
                <div class="form-group__item">
                    <input class="form-group__item-input" type="text" name="name" value="" placeholder="ユーザー名">
                    <div class="error-message">
                        
                    </div>
                </div>

                <div class="form-group__item">
                    <input class="form-group__item-input" type="text" name="email" value="" placeholder="メールアドレス">
                    <div class="error-message">
                        
                    </div>
                </div>

                <div class="form-group__item">
                    <input class="form-group__item-input" type="password" name="password" value="" placeholder="パスワード">
                    <div class="error-message">
                        
                    </div>
                </div>

                <button class="form-btn white" type="submit">
                    新規登録
                </button>
            </form>
        </div>
    </main>
</template>

<script setup>
import '~/assets/css/auth_form.css'
</script>