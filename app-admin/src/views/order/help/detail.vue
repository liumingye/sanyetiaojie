<template>
  <div style="padding-bottom:100px;">
    <div v-loading="loading">
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
              <span class="gray9">案件状态：</span>
              {{ detail.state_text }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9" v-if="detail.user && detail.user.nickName">用户：</span>
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
      <div class="common-form">留言</div>
      <el-row>
        <el-col>
          <div class="pb16">{{ detail.text }}</div>
        </el-col>
      </el-row>
      <div class="common-form mt10" v-if="detail.image.length > 0">附件</div>
      <el-row>
        <el-col v-for="(item,index) in detail.image" :key="index" class="file-col mr10 mb10">
          <a :href="item.file_path" target="_blank">
            <video :src="item.file_path" v-if="item.file_type == 'video'"></video>
            <el-image :src="item.thumb_path ? item.thumb_path : item.file_path" v-else-if="item.file_type == 'image'"></el-image>
            <el-image :src="fileimg_url" v-else></el-image>
            <el-tooltip effect="dark" :content="item.real_name" placement="top">
              <span style="display:block;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:center;">{{item.real_name}}</span>
            </el-tooltip>
          </a>
          <el-button @click="delImage(item)" type="text" :loading="loading" style="display:block;width:100%;padding:2px;">删除</el-button>
        </el-col>
      </el-row>
      <div class="common-form mt10">备注</div>
      <el-row>
        <el-col>
          <el-input type="textarea" clearable maxlength="1000" show-word-limit :autosize="{minRows:4}" placeholder="在这里输入..." v-model="remark" name="remark" @blur="editInfo" />
        </el-col>
      </el-row>
      <div class="common-form mt10" v-if="detail.status==1">选择人员</div>
      <div class="common-form mt10" v-else-if="detail.status==2">操作</div>
      <el-row v-if="detail.status==1 && detail.way==0">
        <el-col class="pb16">
          <el-tree :data="userList" show-checkbox node-key="id" ref="userList" highlight-current :props="defaultProps"></el-tree>
        </el-col>
        <el-button @click="setRole()" type="primary" :loading="loading">确认人员</el-button>
      </el-row>
      <el-row v-if="detail.status==2 && detail.way==3">
        <el-button @click="setComplete()" type="success" :loading="loading">调解完成</el-button>
        <el-button @click="setWay(0)" :loading="loading">选择其他人员</el-button>
      </el-row>
    </div>
    <div class="common-button-wrapper">
      <el-button type="info" size="small" @click="cancelFunc">返回上一页</el-button>
    </div>
  </div>
</template>

<script>
import HelpApi from "@/api/help.js";
import fileimg from "@/assets/img/icon/file.png";

export default {
  components: {},
  data() {
    return {
      fileimg_url: fileimg,
      /*是否加载完成*/
      loading: true,
      /*案件数据*/
      detail: {
        user: {},
        image: {},
      },
      remark: "",
      userList: null,
      defaultProps: {
        children: "children",
        label: "lable",
      },
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
      let value =
        event.target.nodeName == "TEXTAREA"
          ? event.target.value
          : event.target.textContent;
      if (self.detail[name] === value) {
        return;
      }
      self.loading = true;
      if (self.detail[name] != "") {
        self.detail[name] = value;
      }
      // 取到路由带过来的参数
      const params = self.$route.query.id;
      HelpApi.editInfo(
        {
          id: params,
          name: name,
          value: value,
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
      HelpApi.userList(
        {
          tree: 1,
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
      HelpApi.helpdetail(
        {
          id: params,
        },
        true
      )
        .then((data) => {
          self.loading = false;
          self.detail = data.data.detail;
          self.remark = self.detail.remark;
          if (
            self.detail.status == 1 &&
            self.detail.way == 0 &&
            self.userList == null
          ) {
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
      HelpApi.setWay(
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
      HelpApi.setComplete(
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
          self.getData();
        })
        .catch((error) => {
          self.loading = false;
        });
    },
    /*设置人员*/
    setRole() {
      let self = this;
      self.loading = true;
      let id = self.$route.query.id;
      HelpApi.setRole(
        {
          id: id,
          userlist: self.$refs.userList.getCheckedKeys(),
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
    /*删除附件*/
    delImage(item) {
      let self = this;
      let id = item.id;
      self
        .$confirm("此操作将永久删除该记录, 是否继续?", "提示", {
          confirmButtonText: "确定",
          cancelButtonText: "取消",
          type: "warning",
        })
        .then(() => {
          self.loading = true;
          HelpApi.delImage(
            {
              id: id,
            },
            true
          )
            .then((data) => {
              self.loading = false;
              if (data.code == 1) {
                self.$message({
                  message: "删除成功",
                  type: "success",
                });
                self.getData();
              }
            })
            .catch((error) => {
              self.loading = false;
            });
        })
        .catch(() => {
          self.loading = false;
        });
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
  /deep/ .el-image {
    width: 100px;
    height: 100px;
  }
  /deep/ .el-image__error {
    width: 100px;
    height: 100px;
  }
  /deep/ img,
  /deep/ video {
    width: 100px;
    height: 100px;
    background-color: #fafafa;
    object-fit: cover;
  }
}
</style>
