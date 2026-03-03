<template>
<div class="breadcrumb-card">
<ul class="breadcrumb-modern">
<li>
<router-link to="/">
 Kezdőlap
</router-link>

</li>

<li
v-for="(crumb,index) in breadcrumbs"
:key="index"
>

<router-link
v-if="index < breadcrumbs.length-1"
:to="crumb.path"
>

{{crumb.label}}

</router-link>

<span v-else class="active">
{{crumb.label}}
</span>
</li>
</ul>
</div>
</template>
<script>

export default{

computed:{

breadcrumbs(){

let routes=this.$route.matched
.filter(r=>r.meta?.breadcrumb)

return routes.map(r=>({
label:r.meta.breadcrumb,
path:r.path
}))

}

}

}

</script>

<style scoped>

.breadcrumb-wrapper{

background:rgba(255,255,255,0.8);

backdrop-filter:blur(10px);

border-radius:12px;

padding:12px 16px;

margin-bottom:20px;

box-shadow:0 3px 10px rgba(0,0,0,0.15);

}

</style>