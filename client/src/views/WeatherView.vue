
<template>

<div class="card">

<h2>Időjárás</h2>

<div v-if="weather">

<p>Hőmérséklet: {{weather.temperature}} °C</p>
<p>Szél: {{weather.windspeed}} km/h</p>

</div>

</div>

</template>

<script>

import axios from 'axios'

export default{

data(){
return{weather:null}
},

async mounted(){

const res = await axios.get(
'https://api.open-meteo.com/v1/forecast?latitude=47.5&longitude=19&current_weather=true'
)

this.weather = res.data.current_weather

}

}

</script>

<style scoped>
.card{
background:rgba(0,0,0,0.5);
padding:30px;
margin:40px;
border-radius:20px;
}
</style>
