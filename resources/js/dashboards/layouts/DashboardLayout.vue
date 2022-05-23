<template>
    <div class="layout">
        <input type="text" v-model="msg">
        <h3> {{msg}} </h3>
    </div>
</template>

<script>
export default {
    name: "dashboardApp",
    data: () => ({
        msg: "Hello Vue!"
    }),
}
</script>