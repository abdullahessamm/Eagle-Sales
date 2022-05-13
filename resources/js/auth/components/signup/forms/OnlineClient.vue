<template>
    <div id="online-client-info" class="user-info">
        <div class="input-container">
            <input type="text" class="eagle-sales-input" name="l1_address" placeholder="l_address">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> Error msg </div>
        </div>
        <div class="input-container">
            <input type="text" class="eagle-sales-input" name="l2_address" placeholder="l_address">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> Error msg </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'OnlineClient',
}
</script>