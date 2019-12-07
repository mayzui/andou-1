<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>广告管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="<?php echo e(route('banner.add')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增广告</button></a>
                <form method="post" action="<?php echo e(route('banner.index')); ?>" name="form">

                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>描述</th>
                            <th>地址</th>
                            <th>图片</th>
                            <th>排序</th>
                            <th>广告位置</th>
                            <th>发布</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item->id); ?></td>
                                <td><?php echo e($item->desc); ?></td>
                                <td><?php echo e($item->url); ?></td>
                                <td><img src="<?php echo e(env('IMAGE_PATH_PREFIX')); ?><?php echo e($item->img); ?>" alt="" style="width: 50px;height: 50px;"></td>
                                <td><?php echo e($item->sort); ?></td>
                                <td><?php echo e($item->position->name); ?></td>
                                <td>
                                    <?php if($item->status == 1): ?>
                                        <span class="text-info">发布</span>
                                    <?php else: ?>
                                        <span class="text-danger">未发布</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->created_at); ?></td>
                                <td><?php echo e($item->updated_at); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if($item->status == 0): ?>
                                            <a href="<?php echo e(route('banner.status',['status'=>1,'id'=>$item->id])); ?>"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 发布</button></a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('banner.status',['status'=>0,'id'=>$item->id])); ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 关闭</button></a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('banner.update',$item->id)); ?>">
                                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                        </a>
                                        <a href="<?php echo e(route('banner.delete',$item->id)); ?>"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo e($list); ?>

                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>