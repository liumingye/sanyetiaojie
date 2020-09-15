<template>
  <div class="common-header">
    <div class="breadcrumb">
      <!--标题显示-->
      <div class="baseInfo-left-base">
        <span class="name">{{ menu_title }}</span>
      </div>
      <!--右边操作-->
      <div class="header-navbar">
        <div class="header-navbar-icon">
          <span class="gray">当前版本：{{ baseInfo.version }}</span>
        </div>
        <!--操作员账号-->
        <div class="header-navbar-icon">
          <span class="ml4">
            <Icon :iconname="'#icon-geren9'"></Icon>
          </span>
          <span class="text ml4">太湖街道一码解忧，欢迎您！{{ baseInfo.user.user_name }}</span>
        </div>
        <div class="header-navbar-icon"><span class="y-line"></span></div>
        <!--修改密码-->
        <a href="javascript:void(0);" class="header-navbar-icon link" @click="passwordFunc()"><span class="text">修改密码</span></a>
        <!--退出登录-->
        <a href="javascript:void(0);" class="header-navbar-icon login-out link" @click="login_out()">
          <Icon :iconname="'#icon-tuichu'"></Icon>
          <span class="text ml4">退出</span>
        </a>
      </div>
    </div>

    <!--修改密码弹窗-->
    <UpdatePassword v-if="is_password" @close="closeFunc"></UpdatePassword>

  </div>
</template>

<script>
  import bus from '@/utils/eventBus.js';
  import AuthApi from '@/api/auth.js';
  import UserApi from '@/api/user.js';
  import {
    setCookie,
    delCookie,
    getCookie,
    deleteSessionStorage
  } from '@/utils/base.js';
  import UpdatePassword from './part/UpdatePassword.vue';
  export default {
    components: {
      UpdatePassword
    },
    data() {
      return {
        /*菜单名称*/
        menu_title: '菜单',
        /*切换选中*/
        activeValue: 0,
        /*是否修改密码*/
        is_password: false
      };
    },
    inject: ['baseInfo'],
    created() {

      /*监听菜单传过来的值*/
      bus.$on('menuName', res => {
        this.menu_title = res;
      });

      //发送给其它组件头部加载完成
      bus.$emit('headLoad', true);


    },
    beforeDestroy() {
      /*离开删除监听*/
      bus.$off('menuName');
    },
    mounted() {},
    methods: {

      /*退出登录*/
      login_out() {
        this.$confirm('此操作将退出登录, 是否继续?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          })
          .then(() => {
            UserApi.loginOut({}, true)
              .then(data => {
                /*删除登录状态*/
                delCookie('isLogin');
                /*删除基本信息*/
                delCookie('baseInfo');
                /*删除用户菜单*/
                deleteSessionStorage('rolelist');
                /*删除用户权限*/
                deleteSessionStorage('authlist');
                /*返回登录页*/
                this.$router.replace({
                  path: '/login'
                });
                /*删除stores数据*/
                this.$store.commit('user/setState', {
                  key: 'roles',
                  val: null
                });
                /*刷新页面*/
                location.reload();
              })
              .catch(error => {});
          })
          .catch((error) => {
            console.log(error);
          });
      },

      /*打开修改密码弹窗*/
      passwordFunc() {
        this.is_password = true;
      },

      /*关闭修改密码弹窗*/
      closeFunc() {
        this.is_password = false;
      }
    }
  };

</script>

<style lang="scss" scoped="scoped">
  .common-header .el-tabs__nav-wrap::after {
    display: none;
  }

  .common-header .el-tabs-container {
    margin-left: 20px;
    padding-left: 20px;
    border-left: 1px solid #EEEEEE;
  }

  .common-header .el-tabs__header {
    margin-bottom: 0;
  }

  .y-line {
    width: 2px;
    background: #CCCCCC;
    height: 12px;
  }

</style>
