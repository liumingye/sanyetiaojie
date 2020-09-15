<template>
  <div class="right-content pr">
    <!--头部-->
    <Head></Head>
    <!--内容区域-->
    <div class="right-content-box" ref="rightContentBox">
      <div class="subject-wrap">
        <router-view />
      </div>
    </div>
    <el-backtop target=".right-content-box" :visibility-height="10" :right="30" :bottom="60" style="z-index:9999999"></el-backtop>
  </div>
</template>

<script>
import Head from "@/views/layout/Head.vue";
export default {
  components: {
    Head,
  },
  data() {
    return {};
  },
  created() {},
  methods: {},
};
</script>
