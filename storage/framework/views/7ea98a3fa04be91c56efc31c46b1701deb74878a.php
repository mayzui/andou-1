<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>管理员管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="<?php echo e(route('banner.add')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增广告</button></a>
                <form method="post" action="<?php echo e(route('banner.index')); ?>" name="form">
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th class="text-center" width="100">ID</th>
                            <th>描述</th>
                            <th>地址</th>
                            <th>图片</th>
                            <th>排序</th>
                            <th>广告位置</th>
                            <th class="text-center">创建时间</th>
                            <th class="text-center">更新时间</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center"><?php echo e($item->id); ?></td>
                                <td><?php echo e($item->desc); ?></td>
                                <td><?php echo e($item->url); ?></td>
                                <td><img src="<?php echo e($item->img); ?>" alt="" style="width: 50px;height: 50px;"></td>
                                <td class="text-center"><?php echo e($item->created_at); ?></td>
                                <td class="text-center"><?php echo e($item->updated_at); ?></td>
                                <td class="text-center">
                                    <?php if($item->status == 1): ?>
                                        <span class="text-navy">正常</span>
                                    <?php elseif($item->status == 2): ?>
                                        <span class="text-danger">锁定</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($item->allow_in == 1): ?>
                                        <a href="<?php echo e(route('admins.allow',['allow_in'=>0,'id'=>$item->id])); ?>"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 可登录</button></a>
                                    <?php elseif($item->allow_in == 0): ?>
                                        <a href="<?php echo e(route('admins.allow',['allow_in'=>1,'id'=>$item->id])); ?>"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-warning"></i>禁止登录</button></a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('admins.edit',$item->id)); ?>">
                                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                        </a>
                                        <?php if($item->status == 2): ?>
                                            <a href="<?php echo e(route('admins.status',['status'=>1,'id'=>$item->id])); ?>"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 恢复</button></a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('admins.status',['status'=>2,'id'=>$item->id])); ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 禁用</button></a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admins.delete',$item->id)); ?>"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo e($admins->links()); ?>

                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>