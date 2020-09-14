<template>
  <div style="height: calc(100vh - 160px);">
    <el-row style="height: 100%;" v-loading="loading">
      <el-col :lg="{span: 12, offset: 6}" style=" height: 100%;display: flex;flex-direction: column;">
        <el-page-header @back="cancelFunc" :content="title" style="padding-bottom: 16px;"></el-page-header>
        <div id="dataShow" :class="[browser==='Chrome'? 'dataShowCM': 'dataShowFF']">
          <el-button v-if="radioInfoList.current_page * radioInfoList.per_page < radioInfoList.total" type="text" style="text-align: center;display: block;width: 100%;padding-top: 0;" @click="loadMore" v-loading="loading">加载更多...</el-button>
          <div v-for="(item,index) in radioInfoList.data" :key="index" style="background-color: #f5f5f5; padding:0 24px 16px;">
            <el-row v-if="item.is_user==0">
              <el-row style="margin-bottom: 16px" v-if="(index != 0 && new Date(radioInfoList.data[index].create_time).getTime() - new Date(radioInfoList.data[index - 1].create_time).getTime() > 60000) || index == 0">
                <el-col style="text-align: center;">
                  <span class="time">{{ formatDate(item.create_time) }}</span>
                </el-col>
              </el-row>
              <el-avatar shape="square" :src="item.user.avatarUrl || avatarimg_url" style="float:right"></el-avatar>
              <span class="aa">{{ item.text }}</span>
            </el-row>
            <el-row v-if="item.is_user==1">
              <el-row style="margin-bottom: 16px" v-if="(index != 0 && new Date(radioInfoList.data[index].create_time).getTime() - new Date(radioInfoList.data[index - 1].create_time).getTime() > 60000) || index == 0">
                <el-col style="text-align: center;">
                  <span class="time">{{ formatDate(item.create_time) }}</span>
                  <br>
                </el-col>
              </el-row>
              <el-avatar shape="square" :src="item.user.avatarUrl || avatarimg_url"></el-avatar>
              <el-row class="bb">
                <el-col class="name">
                  <span>{{ item.user.nickName }}</span>
                </el-col>
                <el-col class="text">
                  <span>{{ item.text }}</span>
                </el-col>
              </el-row>
            </el-row>
          </div>
        </div>
        <div>
          <el-divider />
          <el-row>
            <el-col>
              <el-input id="condition" v-model="condition" type="textarea" autofocus clearable maxlength="1000" show-word-limit :autosize="{minRows:3,maxRows:6}" resize="none" placeholder="在这里输入..." @keyup.enter.native="submitSearch" />
            </el-col>
          </el-row>
          <el-row style="margin: 4px">
            <el-button id="submit" type="primary" @click="submitSearch" v-auth="'/notice/notice/send'">发送</el-button>
          </el-row>
        </div>
      </el-col>
    </el-row>
    <div class="common-button-wrapper">
      <el-button type="info" size="small" @click="cancelFunc">返回上一页</el-button>
    </div>
  </div>
</template>

<script>
  import NoticeApi from '@/api/notice.js';
  import avatarimg from "@/assets/img/avatar.jpg";
  import {formatDate} from "@/utils/DateTime.js";

  export default {
    data() {
      return {
        avatarimg_url: avatarimg,
        title: '消息页面',
        condition: '',
        radioInfoList: {
          data: []
        },
        browser: 'Chrome',
        page: 1
      }
    },
    created() {
      var self = this;
      self.browser = self.myBrowser()
      let title = this.$route.query.title;
      if(title){
        self.title = title;
      }
      self.getData(function () {
        self.scrollToBottom();
      }, self.page);
    },
    methods: {
      formatDate(date) {
        return formatDate(new Date(date), 'yyyy年MM月dd日 hh:mm');
      },
      getData(cb, page) {
        let self = this;
        self.loading = true;
        // 取到路由带过来的参数
        let id = this.$route.query.id;
        NoticeApi.noticeDetail({
              id: id,
              page: page
            },
            true
          )
          .then((data) => {
            self.page = page;
            self.radioInfoList.total = data.data.data.total;
            self.radioInfoList.per_page = data.data.data.per_page;
            self.radioInfoList.current_page = data.data.data.current_page;
            self.radioInfoList.last_page = data.data.data.last_page;
            if (self.page == 1) {
              self.radioInfoList.data = data.data.data.data;
            } else {
              self.radioInfoList.data = data.data.data.data.concat(self.radioInfoList.data);
            }
            self.loading = false;
            cb && cb();
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      // 滚动到底部
      scrollToBottom() {
        this.$nextTick(() => {
          const div = document.getElementById('dataShow')
          div.scrollTop = div.scrollHeight
        })
      },
      // 去掉回车换行符
      clearBr(key) {
        key = key.replace(/<\/?.+?>/g, '')
        key = key.replace(/[\r\n]/g, '')
        return key
      },
      submitSearch() {
        let self = this;
        let id = self.$route.query.id;
        let text = self.clearBr(self.condition)
        // 取到路由带过来的参数
        NoticeApi.noticeSend({
              id: id,
              text: text,
            },
            true
          )
          .then((data) => {

          })
          .catch((error) => {

          });
        self.radioInfoList.data.push({
          "text": text,
          "is_user": 0,
          "date": new Date().toLocaleDateString().replace(/\//g, '-'),
          "time": new Date().toLocaleTimeString(),
          "user": {
            "nickName": "系统"
          }
        })
        self.scrollToBottom();
        self.condition = '';
      },
      /*返回*/
      cancelFunc() {
        this.$router.go(-1);
      },
      loadMore() {
        let self = this;
        let div = document.getElementById('dataShow')
        let height = div.scrollHeight
        self.getData(null, self.page + 1);
      },
      myBrowser() {
        var userAgent = navigator.userAgent // 取得浏览器的userAgent字符串
        var isOpera = userAgent.indexOf('Opera') > -1 // 判断是否Opera浏览器
        var isIE = userAgent.indexOf('compatible') > -1 &&
          userAgent.indexOf('MSIE') > -1 && !isOpera // 判断是否IE浏览器
        var isEdge = userAgent.indexOf('Edge') > -1 // 判断是否IE的Edge浏览器
        var isFF = userAgent.indexOf('Firefox') > -1 // 判断是否Firefox浏览器
        var isSafari = userAgent.indexOf('Safari') > -1 &&
          userAgent.indexOf('Chrome') === -1 // 判断是否Safari浏览器
        var isChrome = userAgent.indexOf('Chrome') > -1 &&
          userAgent.indexOf('Safari') > -1 // 判断Chrome浏览器

        if (isIE) {
          var reIE = new RegExp('MSIE (\\d+\\.\\d+);')
          reIE.test(userAgent)
          var fIEVersion = parseFloat(RegExp['$1'])
          if (fIEVersion === 7) {
            return 'IE7'
          } else if (fIEVersion === 8) {
            return 'IE8'
          } else if (fIEVersion === 9) {
            return 'IE9'
          } else if (fIEVersion === 10) {
            return 'IE10'
          } else if (fIEVersion === 11) {
            return 'IE11'
          } else {
            return '0'
          } // IE版本过低
          // eslint-disable-next-line no-unreachable
          return 'IE'
        }
        if (isOpera) {
          return 'Opera'
        }
        if (isEdge) {
          return 'Edge'
        }
        if (isFF) {
          return 'FF'
        }
        if (isSafari) {
          return 'Safari'
        }
        if (isChrome) {
          return 'Chrome'
        }
      }
    }
  }

</script>

<style>
  /*<!--非Chrome 隐藏滚动条-->*/
  .dataShowFF {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 0 8px;
    background-color: #f5f5f5;
    scrollbar-width: none;
  }

  /*<!--Chrome 隐藏滚动条-->*/
  .dataShowCM {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 0 8px;
    background-color: #f5f5f5;
  }

  .aa {
    float: right;
    position: relative;
    max-width: calc(100% - 72px);
    margin-right: 12px;
    letter-spacing: 2px;
    min-height: 35px;
    font-size: 15px;
    padding: 10px;
    text-align: left;
    word-break: break-all;
    background-color: #9eea6a;
    color: #000;
    border-radius: 4px;
    box-shadow: 0px 1px 7px -5px #000;
  }

  .aa:after {
    content: "";
    border-top: solid 5px #00800000;
    border-left: solid 8px #9eea6a;
    border-right: solid 8px #00800000;
    border-bottom: solid 5px #00800000;
    position: absolute;
    top: 10px;
    left: 100%;
  }

  .bb {
    display: inline-block;
    max-width: calc(100% - 72px);
    margin-left: 12px;
  }
  .bb .name{
    margin-bottom: 2px;
  }
  .bb .text span:hover{
    background-color: #f6f6f6;
  }
  .bb .text span:hover:after{
    border-right: solid 8px #f6f6f6;
  }
  .bb .text span {
    display: inline-block;
    position: relative;
    letter-spacing: 2px;
    min-height: 35px;
    padding: 10px;
    font-size: 15px;
    word-break: break-all;
    background-color: white;
    color: #000;
    border-radius: 4px;
    box-shadow: 0px 1px 7px -5px #000;
  }

  .bb .text span:after {
    content: "";
    border-top: solid 5px #00800000;
    border-left: solid 8px #00800000;
    border-right: solid 8px white;
    border-bottom: solid 5px #00800000;
    position: absolute;
    top: 12px;
    right: 100%;
  }

  .time{
    border-radius: 4px;
    color: #f5f5f5;
    background-color: #dadada;
    padding: 2px 4px;
    font-size: 14px;
  }

  .el-avatar {
    vertical-align: top;
  }
  
  #submit {
    margin-top: 6px;
    float: right;
  }

</style>
