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
              <span class="gray9">用户：</span>
              {{ detail.user.nickName }}
              <span>用户ID：({{ detail.user.user_id }})</span>
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
              {{ detail.name }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">联系方式：</span>
              {{ detail.mobile }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">身份证号：</span>
              {{ detail.idcard }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">诉求：</span>
              {{ detail.appeal }}
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="common-form mt10">纠纷当事人信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">对方姓名：</span>
              {{ detail.mobile }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">对方联系方式：</span>
              {{ detail.mobile }}
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
              {{ detail.text }}
            </div>
          </el-col>
          <el-col>
            <div class="pb16">
              <span class="gray9">所在地区：</span>
              {{ detail.area }}
            </div>
          </el-col>
          <el-col>
            <div class="pb16">
              <span class="gray9">所在位置：</span>
              {{ detail.address }}
            </div>
          </el-col>
        </el-row>
        <div class="common-form mt10" v-if="detail.image.length > 0">附件</div>
        <el-row>
          <el-col v-for="(item,index) in detail.image" :key="index" style="width:auto;">
            <a :href="item.file_path" target="_blank">
              <el-image class="mr10 mb10" style="width: 200px; height: 200px" :src="item.file_path"></el-image>
            </a>
          </el-col>
        </el-row>
      </div>
    </div>
    <div v-loading="loading" v-else-if="activeName=='schedule'">
      <el-timeline style="margin-top:10px">
        <el-timeline-item
          v-for="(activity, index) in detail.info"
          :key="index"
          :timestamp="activity.create_time"
        >{{activity.text}}</el-timeline-item>
      </el-timeline>
    </div>
    <div v-loading="loading" v-else-if="activeName=='operate'">
      <div class="common-form mt10" v-if="detail.status==1">调解方式</div>
      <div class="common-form mt10" v-else>操作</div>
      <el-row v-if="detail.status==1">
        <el-button @click="setWay(1)" :loading="loading">电话调解</el-button>
        <el-button @click="setWay(2)" :loading="loading">律师调解</el-button>
      </el-row>
      <el-row v-if="detail.status==2 && detail.way==1">
        <el-button @click="setComplete()" type="success" :loading="loading">调解完成</el-button>
        <el-button @click="setWay(0)" :loading="loading">选择其他调解</el-button>
      </el-row>
      <el-row v-if="detail.status==2 && detail.way==2">
        <el-col class="pb16">
          <span>选择委员会：</span>
          <el-select
            style="width:50%"
            v-model="committee.value"
            multiple
            filterable
            remote
            reserve-keyword
            placeholder="请输入委员会姓名"
            :remote-method="remoteCommittee"
            :loading="loading"
          >
            <el-option
              v-for="item in committee.list"
              :key="item.shop_user_id"
              :label="item.real_name+'('+item.shop_user_id+')'"
              :value="item.shop_user_id"
            ></el-option>
          </el-select>
        </el-col>
        <el-col class="pb16">
          <span>选择律师：</span>
          <el-select
            style="width:50%"
            v-model="lawyer.value"
            multiple
            filterable
            remote
            reserve-keyword
            placeholder="请输入律师姓名"
            :remote-method="remoteLawyer"
            :loading="loading"
          >
            <el-option
              v-for="item in lawyer.list"
              :key="item.id"
              :label="item.name+'('+item.id+')'"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-col>
        <el-button @click="setRole()" type="primary" :loading="loading">确认人员</el-button>
        <el-button @click="setWay(0)" :loading="loading">选择其他调解</el-button>
      </el-row>
      <el-row v-if="detail.status==2 && detail.way==3">
        <el-button @click="getWord(1)" type="primary" :loading="loading">立案书</el-button>
        <el-button @click="setComplete()" type="success" :loading="loading">调解完成</el-button>
        <el-button @click="setWay(0)" :loading="loading">选择其他调解</el-button>
      </el-row>
      <el-row v-if="detail.status==3">
        <el-button @click="getWord(2)" :loading="loading">结案书</el-button>
      </el-row>
    </div>
    <div class="common-button-wrapper">
      <el-button type="info" size="small" @click="cancelFunc">返回上一页</el-button>
    </div>
  </div>
</template>

<script>
import OrderApi from "@/api/order.js";
import { deepClone } from "@/utils/base.js";
export default {
  components: {},
  data() {
    return {
      activeName: "info",
      active: 0,
      /*是否加载完成*/
      loading: true,
      /*订单数据*/
      detail: {
        user: {},
        image: {},
      },
      /*是否打开添加弹窗*/
      open_add: false,
      /*一页多少条*/
      pageSize: 20,
      /*一共多少条数据*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1,
      /*发货*/
      form: {},
      forms: {
        is_cancel: 1,
      },
      form1: {
        order: {
          extract_status: 1,
        },
      },
      order: {},
      delivery_type: 0,
      /*配送方式*/
      exStyle: [],
      /*门店列表*/
      shopList: [],
      /*当前编辑的对象*/
      userModel: {},
      /*时间*/
      create_time: "",
      /*快递公司列表*/
      expressList: [],
      shopClerkList: [],
      /*是否打开编辑弹窗*/
      open_edit: false,
      committee: {
        value: [],
        list: [],
      },
      lawyer: {
        value: [],
        list: [],
      },
    };
  },
  created() {
    /*获取列表*/
    this.getParams();
  },
  methods: {
    next() {
      if (this.active++ > 4) this.active = 0;
    },
    /*获取参数*/
    getParams() {
      let self = this;
      self.loading = true;
      // 取到路由带过来的参数
      const params = this.$route.query.id;
      OrderApi.orderdetail(
        {
          id: params,
        },
        true
      )
        .then((data) => {
          self.loading = false;
          self.detail = data.data.detail;
        })
        .catch((error) => {
          self.loading = false;
        });
    },
    /*返回*/
    cancelFunc() {
      this.$router.go(-1);
    },
    /*清空表单*/
    cleanSelect() {
      let self = this;
      self.committee = {
        value: [],
        list: [],
      };
      self.lawyer = {
        value: [],
        list: [],
      };
    },
    /*设置调解方式*/
    setWay(way) {
      let self = this;
      self.loading = true;
      self.cleanSelect();
      let id = this.$route.query.id;
      OrderApi.setway(
        {
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
          self.getParams();
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
      OrderApi.setComplete(
        {
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
          self.getParams();
        })
        .catch((error) => {
          self.loading = false;
        });
    },
    remoteCommittee(e) {
      let self = this;
      if (e == "") {
        self.committee.list = [];
        return;
      }
      OrderApi.searchCommittee(
        {
          text: e,
        },
        true
      )
        .then((data) => {
          self.committee.list = data.data.data;
        })
        .catch((error) => {});
    },
    remoteLawyer(e) {
      let self = this;
      if (e == "") {
        self.lawyer.list = [];
        return;
      }
      OrderApi.searchLawyer(
        {
          text: e,
        },
        true
      )
        .then((data) => {
          self.lawyer.list = data.data.data;
        })
        .catch((error) => {});
    },
    setRole() {
      let self = this;
      self.loading = true;
      let id = this.$route.query.id;
      if (self.committee.value.length != 0) {
        OrderApi.setRole(
          {
            id: id,
            committee: self.committee.value,
            lawyer: self.lawyer.value,
          },
          true
        )
          .then((data) => {
            self.loading = false;
            self.lawyer.list = data.data.data;
            self.getParams();
          })
          .catch((error) => {
            self.loading = false;
          });
      } else {
        self.loading = false;
        self.$message({
          message: "请选择委员会",
          type: "error",
        });
      }
    },
    /*获取WORD*/
    getWord(type) {
      let self = this;
      let id = this.$route.query.id;
      window.open(OrderApi.getWord() + "?id=" + id + "&type=" + type, "_blank");
    },
    /*切换菜单*/
    handleClick(tab, event) {
      let self = this;

      // self.curPage = 1;
      // self.loading = true;
      // self.getData();
    },
    // /*发货*/
    // onSubmit() {
    //   let self = this;
    //   let param = self.form;
    //   let order_id = this.$route.query.order_id;
    //   OrderApi.delivery(
    //     {
    //       param: param,
    //       order_id: order_id,
    //     },
    //     true
    //   )
    //     .then((data) => {
    //       self.loading = false;
    //       self.$message({
    //         message: "恭喜你，发货成功",
    //         type: "success",
    //       });
    //       self.getParams();
    //     })
    //     .catch((error) => {
    //       self.loading = false;
    //     });
    // },
    // /*确认审核*/
    // confirmCancel() {
    //   let self = this;
    //   let order_id = this.$route.query.order_id;
    //   let is_cancel = self.forms.is_cancel;
    //   OrderApi.confirm(
    //     {
    //       order_id: order_id,
    //       is_cancel: is_cancel,
    //     },
    //     true
    //   )
    //     .then((data) => {
    //       self.loading = false;
    //       self.$message({
    //         message: "恭喜你，审核成功",
    //         type: "success",
    //       });
    //       self.getParams();
    //     })
    //     .catch((error) => {
    //       self.loading = false;
    //     });
    // },
    // /*核销*/
    // onSubmit1(e) {
    //   let self = this;
    //   let form1 = self.form1;
    //   form1.order_id = e;
    //   OrderApi.extract(
    //     {
    //       form1,
    //     },
    //     true
    //   )
    //     .then((data) => {
    //       self.loading = false;
    //       self.$message({
    //         message: "恭喜你，操作成功",
    //         type: "success",
    //       });
    //       self.getParams();
    //     })
    //     .catch((error) => {
    //       self.loading = false;
    //     });
    // },

    // /*打开编辑*/
    // editClick(item) {
    //   //                this.userModel = item;
    //   this.userModel = deepClone(item);
    //   this.open_edit = true;
    // },
    // /*关闭弹窗*/
    // closeDialogFunc(e, f) {
    //   if (f == "edit") {
    //     this.open_edit = e.openDialog;
    //     this.getParams();
    //   }
    // },
  },
};
</script>
