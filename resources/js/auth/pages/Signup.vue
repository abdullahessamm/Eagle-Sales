<template>
    <div id="signup-page">
        Signup here
    </div>
</template>

<script>
export default {
    name: 'Signup',

    mounted() {
        let title = document.querySelector('title');
        let appName = title.innerText.slice(0, title.innerText.indexOf(' | '));

        title.innerText = appName + ' | sign up'
    },
}
</script>