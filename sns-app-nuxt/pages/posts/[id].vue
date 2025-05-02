<template>
    <main class="main grid">
        <SideNav></SideNav>
        <div class="home-container white">
            <h2 class="home-ttl">コメント</h2>
            <Message></Message>

            <div class="comment-wrap">
                <h3 class="comment--ttl">コメント</h3>
                <div class="comment-group flex">
                    <p class="comment-name">username</p>
                    <p class="comment-text">texttext</p>
                </div>
            </div>

            <form class="comment-form flex white">
                <div class="comment-form__input-group">
                    <textarea class="comment-form__textarea white" name="comment" rows="1"></textarea>
                    <div class="error-message">
                        message
                    </div>
                </div>
                <button class="form-btn white comment-form-btn" type="submit">コメント</button>
            </form>
        </div>
    </main>
</template>

<script setup>
import '~/assets/css/auth_form.css'
import '~/assets/css/message_detail.css'
</script>