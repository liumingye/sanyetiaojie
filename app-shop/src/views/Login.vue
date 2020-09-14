<template>
<div class="login-bg" :style="'background-image:url(' + bgimg_url + ');'">
    <el-row>
        <el-col :xs="24" :sm="24" :md="11" :lg="14" :xl="14">
            <div style="color: transparent;">lmy</div>
        </el-col>
        <el-col :xs="24" :sm="24" :md="12" :lg="9" :xl="9" class="justify-center">
            <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-position="left" label-width="0px" class="demo-ruleForm login-container">
                <h3 class="title" style="margin-bottom: 20px;">一码解忧后台管理</h3>
                <!--用户名-->
                <el-form-item prop="account">
                    <span class="svg-container">
                        <svg class="icon svg-icon" aria-hidden="true">
                            <use xlink:href="#icon-yonghu" />
                        </svg>
                    </span>
                    <el-input type="text" v-model="ruleForm.account" auto-complete="off" placeholder="请输入用户名" />
                </el-form-item>
                <!--密码-->
                <el-form-item prop="checkPass">
                    <span class="svg-container">
                        <svg class="icon svg-icon" aria-hidden="true">
                            <use xlink:href="#icon-mima" />
                        </svg>
                    </span>
                    <el-input type="password" v-model="ruleForm.checkPass" auto-complete="off" placeholder="请输入密码" show-password />
                </el-form-item>
                <!--登录-->
                <el-form-item>
                    <el-button type="primary" class="login-btn" @click.native.prevent="SubmitFunc" :loading="logining">登录</el-button>
                </el-form-item>
            </el-form>
        </el-col>
        <el-col :xs="24" :sm="24" :md="1" :lg="1" :xl="1">
            <div style="color: transparent;">lmy</div>
        </el-col>
    </el-row>
</div>
</template>

<script>
import bgimg from "@/assets/img/login/bg.jpg";
import UserApi from "@/api/user.js";
import {
    setCookie
} from "@/utils/base.js";
export default {
    data() {
        return {
            /*背景图片*/
            bgimg_url: bgimg,
            /*是否加载完整*/
            logining: false,
            /*表单对象*/
            ruleForm: {
                /*用户名*/
                account: "",
                /*密码*/
                checkPass: "",
            },
            /*验证规则*/
            rules: {
                /*用户名*/
                account: [{
                    required: true,
                    message: "请输入用户名",
                    trigger: "blur"
                }],
                /*密码*/
                checkPass: [{
                    required: true,
                    message: "请输入密码",
                    trigger: "blur"
                }],
            },
        };
    },
    methods: {
        /*登录方法*/
        SubmitFunc(ev) {
            var _this = this;
            this.$refs.ruleForm.validate((valid) => {
                if (valid) {
                    this.logining = true;
                    var Params = {
                        username: this.ruleForm.account,
                        password: this.ruleForm.checkPass,
                    };
                    /*调用登录接口*/
                    UserApi.login(Params, true)
                        .then((res) => {
                            this.logining = false;
                            if (res.code == 1) {
                                /*保存个人信息*/
                                setCookie("userinfo", res.data);
                                /*设置一个登录状态*/
                                setCookie("isLogin", true);
                                /*跳转到首页*/
                                this.$router.push({ path: '/' });
                            } else {
                                this.$message({
                                    message: "登录失败",
                                    type: "error",
                                });
                            }
                        })
                        .catch((error) => {
                            //接口调用方法统一处理
                            this.logining = false;
                        });
                }
            });
        },
    },
};
</script>

<style lang="scss" scoped>
.login-bg {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    background-attachment: fixed;
}

.el-row {
    height: 100vh;
}

.justify-center {
    display: flex;
    justify-content: center;
    height: 100vh;
    flex-flow: column;
}

.login-container {
    position: relative;
    max-width: 100%;
    margin-left: 10%;
    margin-right: 10%;
    padding: 4.5vh;
    overflow: hidden;
    background: url("~@/assets/img/login/form.png");
    background-size: auto;
    background-size: 100% 100%;
    font-size: 14px;

    .title {
        font-size: 54px;
        font-weight: 500;
        color: #fff;
    }

    .svg-container {
        position: absolute;
        top: 6px;
        font-size: 16px;
        color: #d7dee3;
        left: 15px;
        z-index: 999;

        svg {
            height: 16px;
            width: 16px;
            fill: #d7dee3;
        }
    }

    .remember {
        margin: 0px 0px 35px 0px;
    }

    .login-btn {
        display: inherit;
        width: 220px;
        height: 60px;
        margin-top: 5px;
        border: 0;

        &:hover {
            opacity: 0.9;
        }
    }

    .el-form-item /deep/ input {
        height: 48px;
        padding-left: 45px;
        font-size: 14px;
        line-height: 58px;
        color: #606266;
        background: #f6f4fc;
        border: 0;
        caret-color: #606266;
        border: none;

    }

    .el-form-item /deep/ .el-input__suffix {
        right: 10px;
        top: 1px;
    }
}
</style>
