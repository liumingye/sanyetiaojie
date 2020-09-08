<template>
  <div style="padding-bottom:100px;">
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <el-tab-pane label="详情" name="info"></el-tab-pane>
      <el-tab-pane label="进度" name="schedule"></el-tab-pane>
      <el-tab-pane label="操作" name="operate"></el-tab-pane>
    </el-tabs>
    <div v-loading="loading" v-if="activeName=='info'">
      <div class="common-form">基本信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">ID：</span>
              {{ detail.id }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">案件号：</span>
              {{ detail.no }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">案件状态：</span>
              {{ detail.state_text }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">调解次数：</span>
              {{ detail.times }}次
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">用户：</span>
              {{ detail.user.nickName }}
              <span>用户ID：({{ detail.user.user_id }})</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">申请时间：</span>
              {{ detail.create_time }}
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="common-form mt10">申请人信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">姓名：</span>
              <span contenteditable @blur="editInfo" name="name">{{ detail.name }}</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">联系方式：</span>
              <span contenteditable @blur="editInfo" name="mobile">{{ detail.mobile }}</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">地区：</span>
              <span contenteditable @blur="editInfo" name="my_area">{{ detail.my_area }}</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">住址：</span>
              <span contenteditable @blur="editInfo" name="my_address">{{ detail.my_address }}</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">身份证号：</span>
              <span contenteditable @blur="editInfo" name="idcard">{{ detail.idcard }}</span>
            </div>
          </el-col>
          <el-col>
            <div class="pb16">
              <span class="gray9">诉求：</span>
              <span contenteditable @blur="editInfo" name="appeal">{{ detail.appeal }}</span>
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="common-form mt10">纠纷当事人信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">姓名：</span>
              <span contenteditable @blur="editInfo" name="other_name">{{ detail.other_name }}</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">联系方式：</span>
              <span contenteditable @blur="editInfo" name="other_phone">{{ detail.other_phone }}</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">地区：</span>
              <span contenteditable @blur="editInfo" name="other_area">{{ detail.other_area }}</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">住址：</span>
              <span contenteditable @blur="editInfo" name="other_address">{{ detail.other_address }}</span>
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="common-form mt10">纠纷内容</div>
      <div class="table-wrap">
        <el-row>
          <el-col>
            <div class="pb16">
              <span class="gray9">纠纷描述：</span>
              <span contenteditable @blur="editInfo" name="text">{{ detail.text }}</span>
            </div>
          </el-col>
          <el-col>
            <div class="pb16">
              <span class="gray9">所在地区：</span>
              <span contenteditable @blur="editInfo" name="area">{{ detail.area }}</span>
            </div>
          </el-col>
          <el-col>
            <div class="pb16">
              <span class="gray9">所在位置：</span>
              <span contenteditable @blur="editInfo" name="address">{{ detail.address }}</span>
            </div>
          </el-col>
        </el-row>
        <div class="common-form mt10" v-if="detail.image.length > 0">附件</div>
        <el-row>
          <el-col v-for="(item,index) in detail.image" :key="index" class="file-col">
            <a :href="item.file_path" target="_blank">
              <video class="mr10 mb10" :src="item.file_path" v-if="item.file_type == 'video'"></video>
              <el-image class="mr10 mb10" :src="item.file_path" v-else-if="item.file_type == 'image'"></el-image>
              <el-image class="mr10 mb10" :src="fileimg_url" v-else></el-image>
            </a>
          </el-col>
        </el-row>
      </div>
      <div class="common-form mt10">备注</div>
      <el-row>
        <el-col>
          <el-input type="textarea" clearable maxlength="1000" show-word-limit :autosize="{minRows:4}" placeholder="在这里输入..." v-model="remark" name="remark" @blur="editInfo" />
        </el-col>
      </el-row>
    </div>
    <div v-loading="loading" v-else-if="activeName=='schedule'">
      <div class="common-form">进度</div>
      <el-timeline>
        <div v-for="(activity, index) in detail.info" :key="index">
          <div v-if="(index != 0 && detail.info[index].times != detail.info[index-1].times) || index == 0">
            <h3 style="margin:12px 0;">第{{activity.times}}次调解</h3>
            <el-timeline-item :timestamp="activity.create_time" :color="activity.status == 1 ? '#e4e7ed' : activity.status == 2 ? '#0bbd87' : activity.status == 3 ? '#409eff' : '#F56C6C'">
              {{activity.text}}
              <el-link type="danger" @click="delSchedule(activity.id)">删除</el-link>
            </el-timeline-item>
          </div>
          <el-timeline-item :timestamp="activity.create_time" :color="activity.status == 1 ? '#e4e7ed' : activity.status == 2 ? '#0bbd87' : activity.status == 3 ? '#409eff' : '#F56C6C'" v-else>
            {{activity.text}}
            <el-link type="danger" @click="delSchedule(activity.id)">删除</el-link>
          </el-timeline-item>
        </div>
      </el-timeline>
      <div class="common-form">操作</div>
      <el-row>
        <el-button @click="addSchedule()" :loading="loading">添加进度</el-button>
      </el-row>
    </div>
    <div v-loading="loading" v-else-if="activeName=='operate'">
      <div class="common-form" v-if="detail.status==1">调解方式</div>
      <div class="common-form" v-else>操作</div>
      <el-row v-if="detail.status==1">
        <el-button @click="setWay(1)" :loading="loading">电话调解</el-button>
        <el-button @click="setWay(2)" :loading="loading">调委会调解</el-button>
      </el-row>
      <el-row v-if="detail.status==2 && detail.way==1">
        <el-button @click="setComplete()" type="success" :loading="loading" v-auth="'/order.mediate/setcomplete'">调解完成</el-button>
        <el-button @click="setFail(1)" type="danger" :loading="loading" v-auth="'/order.mediate/setfail'">调解失败</el-button>
        <el-button @click="setWay(0)" :loading="loading">选择其他调解</el-button>
      </el-row>
      <el-row v-if="detail.status==2 && detail.way==2">
        <el-col class="pb16">
          <span>开庭时间：</span>
          <el-date-picker v-model="detail.court_time" type="datetime" placeholder="选择开庭时间"></el-date-picker>
        </el-col>
        <el-col class="pb16">
          <span>开庭地址：</span>
          <el-input v-model="detail.court_address" placeholder="请输入开庭地址" style="width:240px;"></el-input>
        </el-col>
        <el-col class="pb16">
          <div class="mb10">选择人员：</div>
          <el-tree :data="userList" show-checkbox node-key="id" ref="userList" highlight-current :props="defaultProps">
          </el-tree>
        </el-col>
        <el-button @click="setRole()" type="primary" :loading="loading">确认操作</el-button>
        <el-button @click="setWay(0)" :loading="loading">选择其他调解</el-button>
      </el-row>
      <el-row v-if="detail.status==2 && detail.way==3">
        <el-row>
          <el-col v-if="detail.court_time">
            <div class="pb16">
              <span class="gray9">开庭时间：</span>
              {{ detail.court_time }}
            </div>
          </el-col>
          <el-col v-if="detail.court_address">
            <div class="pb16">
              <span class="gray9">开庭地址：</span>
              {{ detail.court_address }}
            </div>
          </el-col>
        </el-row>
        <el-button @click="getWord(1)" type="primary" :loading="loading" v-auth="'/order/mediate/getword'">立案书</el-button>
        <el-button @click="setComplete()" type="success" :loading="loading" v-auth="'/order.mediate/setcomplete'">调解完成</el-button>
        <el-button @click="setFail(2)" type="danger" :loading="loading" v-auth="'/order.mediate/setfail'">调解失败</el-button>
        <el-button @click="setWay(0)" :loading="loading">选择其他调解</el-button>
      </el-row>
      <el-row v-if="detail.status==3">
        <el-button @click="getWord(2)" :loading="loading" v-auth="'/order/mediate/getword'">结案书</el-button>
      </el-row>
    </div>
    <div class="common-button-wrapper">
      <el-button type="info" size="small" @click="cancelFunc">返回上一页</el-button>
    </div>
    <!--添加进度-->
    <AddSchedule v-if="open_addSchedule" @close="closeDialogFunc($event, 'addSchedule')"></AddSchedule>
  </div>
</template>

<script>
  import OrderApi from "@/api/order.js";
  import fileimg from "@/assets/img/icon/file.png";
  import AddSchedule from './dialog/addSchedule.vue';

  export default {
    components: {
      AddSchedule
    },
    data() {
      return {
        fileimg_url: fileimg,
        open_addSchedule: false,
        activeName: "info",
        /*是否加载完成*/
        loading: true,
        /*案件数据*/
        detail: {
          user: {},
          image: {},
          court_time: null,
          court_address: ''
        },
        remark: '',
        userList: null,
        defaultProps: {
          children: 'children',
          label: 'lable'
        }
      };
    },
    created() {
      /*获取列表*/
      this.getData();
    },
    methods: {
      /*编辑详情*/
      editInfo(event) {
        let self = this;
        let name = event.target.attributes.name.value;
        let value = event.target.nodeName == "TEXTAREA" ? event.target.value : event.target.textContent;
        if (self.detail[name] === value) {
          return;
        }
        self.loading = true;
        if (self.detail[name] != '') {
          self.detail[name] = value;
        }
        // 取到路由带过来的参数
        const params = self.$route.query.id;
        OrderApi.editInfo({
              id: params,
              name: name,
              value: value
            },
            true
          )
          .then((data) => {
            self.loading = false;
            if (data.code == 1) {
              self.$message({
                message: data.msg,
                type: "success",
              });
            } else {
              self.$message({
                message: data.msg,
                type: "error",
              });
              self.getData();
            }
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      getUserList() {
        let self = this;
        self.loading = true;
        OrderApi.userList({
              tree: 1
            },
            true
          )
          .then((data) => {
            self.loading = false;
            self.userList = data.data.list;
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      /*获取参数*/
      getData() {
        let self = this;
        self.loading = true;
        // 取到路由带过来的参数
        const params = this.$route.query.id;
        OrderApi.orderdetail({
              id: params,
            },
            true
          )
          .then((data) => {
            self.loading = false;
            self.detail = data.data.detail;
            self.remark = self.detail.remark;
            if (self.detail.court_time == 0) {
              self.detail.court_time = (new Date()).valueOf();
              self.detail.court_time = '';
            }
            if (self.detail.status == 2 && self.detail.way == 2 && self.userList == null) {
              self.getUserList();
            }
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      /*返回*/
      cancelFunc() {
        this.$router.go(-1);
      },
      /*设置调解方式*/
      setWay(way) {
        let self = this;
        self.loading = true;
        let id = this.$route.query.id;
        OrderApi.setWay({
              id: id,
              way: way,
            },
            true
          )
          .then((data) => {
            self.loading = false;
            self.$message({
              message: data.msg,
              type: "success",
            });
            self.getData();
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      /*调解完成*/
      setComplete() {
        let self = this;
        self.loading = true;
        let id = this.$route.query.id;
        OrderApi.setComplete({
              id: id,
            },
            true
          )
          .then((data) => {
            self.loading = false;
            self.$message({
              message: data.msg,
              type: "success",
            });
            self.getData();
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      /*调解失败*/
      setFail(type) {
        let self = this;
        self.loading = true;
        let id = this.$route.query.id;
        OrderApi.setFail({
              id: id,
              type: type
            },
            true
          )
          .then((data) => {
            self.loading = false;
            self.$message({
              message: data.msg,
              type: "success",
            });
            self.getData();
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      setRole() {
        let self = this;
        self.loading = true;
        let id = self.$route.query.id;
        OrderApi.setRole({
              id: id,
              court_time: self.detail.court_time,
              court_address: self.detail.court_address,
              userlist: self.$refs.userList.getCheckedKeys()
            },
            true
          )
          .then((data) => {
            self.loading = false;
            self.getData();
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      /*获取WORD*/
      getWord(type) {
        let self = this;
        let id = self.$route.query.id;
        window.open(OrderApi.getWord() + "?id=" + id + "&type=" + type, "_blank");
      },
      /*切换菜单*/
      handleClick(tab, event) {
        let self = this;
      },
      addSchedule() {
        this.open_addSchedule = true;
      },
      delSchedule(id) {
        let self = this;
        self.loading = true;
        OrderApi.delSchedule({
              id: id
            },
            true
          )
          .then((data) => {
            self.loading = false;
            self.$message({
              message: data.msg,
              type: "success",
            });
            self.getData();
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      /*关闭弹窗*/
      closeDialogFunc(e, f) {
        if (f == 'addSchedule') {
          this.open_addSchedule = e.openDialog;
          if (e.type == 'success') {
            this.getData();
          }
        }
      },

    },
  };

</script>

<style lang="scss" scoped>
  span[contenteditable] {
    display: inline-block;
    min-width: 40px;
  }

  .file-col {
    width: auto;
  }

  .file-col /deep/ img,
  .file-col /deep/ video {
    width: 100px;
    height: 100px;
    background-color: #fafafa;
    object-fit: cover;
  }
  /deep/ .el-timeline-item__tail{
    display: block!important;
  }

</style>
