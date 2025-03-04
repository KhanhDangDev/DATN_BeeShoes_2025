import PropTypes from 'prop-types';
import { useCallback, useEffect, useMemo, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import * as Yup from 'yup';
// form
import { yupResolver } from '@hookform/resolvers/yup';
import { useForm, Controller } from 'react-hook-form';
// @mui
import { styled } from '@mui/material/styles';
import {
  Button,
  Box,
  Divider,
  Card,
  Checkbox,
  Grid,
  Stack,
  TextField,
  Typography,
  MenuItem,
  IconButton,
  Tooltip,
} from '@mui/material';
import Autocomplete, { createFilterOptions } from '@mui/material/Autocomplete';
import useFetch from '../../../../hooks/useFetch';
import { ADMIN_API } from '../../../../api/apiConfig';
// routes
import { PATH_DASHBOARD } from '../../../../routes/paths';
// hooks
import useConfirm from '../../../../hooks/useConfirm';
import useNotification from '../../../../hooks/useNotification';
// components
import Iconify from '../../../../components/Iconify';
import { FormProvider, RHFSwitch, RHFEditor, RHFTextField } from '../../../../components/hook-form';
import Label from '../../../../components/Label';
import { ProductStatusTab, AttributeStatus } from '../../../../constants/enum';
import { convertProductStatusBoolean } from '../../../../utils/ConvertEnum';
import { IconArrowDownAutocomplete } from '../../../../components/IconArrow';
import { formatCurrencyVnd, formatNumber } from '../../../../utils/formatCurrency';
import ProductUploadMultiFile from './upload/ProductUploadMultiFile';

// ----------------------------------------------------------------------

const IMAGE_MIN_LENGTH = 4;

const LabelStyleDescription = styled(Typography)(({ theme }) => ({
  ...theme.typography.subtitle2,
  color: theme.palette.text.secondary,
  marginBottom: theme.spacing(1),
}));

const LabelStyle = styled(Typography)(({ theme }) => ({
  ...theme.typography.subtitle2,
  color: theme.palette.text.secondary,
  marginBottom: theme.spacing(0.5),
}));

const LabelStyleHeader = styled(Typography)(({ theme }) => ({
  ...theme.typography.subtitle2,
  color: theme.palette.text.black,
  fontSize: 16.5,
}));

ProductNewEditForm.propTypes = {
  isEdit: PropTypes.bool,
  currentProduct: PropTypes.object,
};

export default function ProductNewEditForm({ isEdit, currentProduct, onUpdateData }) {
  console.log(currentProduct);

  const [productVarians, setProductVarians] = useState(currentProduct?.variants || []);
  const [productImages, setProductImages] = useState([]);
  const [productImageFiles, setProductImageFiles] = useState([]);

  const { onOpenSuccessNotify, onOpenErrorNotify } = useNotification();

  const { id } = useParams();

  const { showConfirm, showConfirmCancel } = useConfirm();

  const { data, setData, formDataFile, post } = useFetch(ADMIN_API.product.attributes);

  console.log(data);

  const navigate = useNavigate();

  const NewProductSchema = Yup.object().shape({
    name: Yup.string()
      .test('max', 'Tên sản phẩm quá dài (tối đa 100 ký tự)', (value) => value.trim().length <= 100)
      .required('Tên sản phẩm không được để trống'),
    code: Yup.string()
      .test('max', 'Mã sản phẩm quá dài (tối đa 20 ký tự)', (value) => value.trim().length <= 20)
      .required('Mã sản phẩm không được để trống'),
    brand: Yup.object().nullable().required('Bạn chưa chọn thương hiệu'),
    categorys: Yup.array().of(Yup.object()).min(1, 'Vui lòng chọn ít nhất 1 danh mục').required(),
  });

  useEffect(() => {
    setProductVarians(currentProduct?.variants || []);
    setProductImages(currentProduct?.images || []);
    setProductImageFiles(currentProduct?.images || []);
}, [currentProduct]);

  const defaultValues = useMemo(
    () => ({
      name: currentProduct?.name || '',
      code: currentProduct?.code || '',
      description: currentProduct?.description || '',
      categorys: currentProduct?.categories || [],
      brand: data.brands ? data.brands.find((item) => item.id === currentProduct?.brandId) : null,
      status: convertProductStatusBoolean(currentProduct?.status),
    }),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [currentProduct]
  );

  const methods = useForm({
    resolver: yupResolver(NewProductSchema),
    defaultValues,
  });

  const {
    reset,
    watch,
    control,
    setValue,
    getValues,
    handleSubmit,
    formState: { isSubmitted },
  } = methods;

  const values = watch();

  const { colors, sizes, name, code, brand, categorys, description, status } = values;

  useEffect(() => {
    if (isEdit && currentProduct) {
      reset(defaultValues);
    }
    if (!isEdit) {
      reset(defaultValues);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isEdit, currentProduct]);

  const isDefault =
    name === '' && code === '' && description === '' && brand === null && status === true && categorys.length === 0;
  // variants?.length === 0;

  // const currentVariantImages = variants?.flatMap(variant => variant.images.map(image => {
  //   return { ...image };
  // }));

  // const currentProductImages = currentProduct?.variants?.flatMap(variant => variant.images.map(image => {
  //   return { ...image };
  // }));

  // const oldProductItems = currentProduct?.variants?.flatMap(variant => variant.variantItems.map(variantItem => {
  //   return {
  //     ...variantItem,
  //   };
  // }));

  // const newProductItems = variants?.flatMap(variant => variant.variantItems.map(variantItem => {
  //   return {
  //     ...variantItem,
  //   };
  // }));
  const stripHtml = (html) => {
    const doc = new DOMParser().parseFromString(html, 'text/html');
    return doc.body.textContent || '';
  };

  const isNotDefaultEdit =
    name?.trim() !== currentProduct?.name ||
    code?.trim() !== currentProduct?.code ||
    stripHtml(description?.trim()) !== currentProduct?.description ||
    brand?.id !== currentProduct?.brandId ||
    status !== (currentProduct?.status === ProductStatusTab.en.IS_ACTIVE);
  // compareArrays(currentProduct?.categories, categorys, "id") ||
  // // compareArrays(currentProduct?.colors, colors, "id") ||
  // // compareArrays(currentProduct?.sizes, sizes, "id") ||
  // compareArrayValues(oldProductItems, newProductItems, "quantity") ||
  // compareArrayValues(oldProductItems, newProductItems, "status") ||
  // compareArrayValues(oldProductItems, newProductItems, "price") ||
  // compareArrayValues(currentProductImages, currentVariantImages, "isDefault") ||
  // compareArrays(oldProductItems, newProductItems, "id") ||
  // compareArrays(currentProductImages, currentVariantImages, "path");

  const handleCancel = () => {
    if (!isDefault && !isEdit) {
      showConfirmCancel(`Xác nhận hủy bỏ thêm mới?`, 'Tất cả thay đổi của bạn sẽ không được lưu!', () =>
        navigate(PATH_DASHBOARD.product.list)
      );
      return;
    }
    if (isNotDefaultEdit && isEdit) {
      showConfirmCancel(`Xác nhận hủy bỏ cập nhật?`, 'Tất cả thay đổi của bạn sẽ không được lưu!', () =>
        navigate(PATH_DASHBOARD.product.list)
      );
      return;
    }
    navigate(PATH_DASHBOARD.product.list);
  };

  const onFinishSaveProduct = (data) => {
    console.log(data);
    onOpenSuccessNotify(`${!isEdit ? 'Thêm mới ' : 'Cập nhật '} sản phẩm thành công!`);
    if (!isEdit) {
      navigate(PATH_DASHBOARD.product.edit(data?.id));
    }
    // else {
    //   onUpdateData(data);
    // }
  };

  const onSubmit = async (data) => {
    const { brand, categorys, ...newData } = data;

    const categoryIds = data?.categorys?.map((item) => item.id);
    const brandId = data?.brand?.id;
    const productItems = productVarians.map((item) => {
      return {
        colorId: item?.colorId?.id,
        sizeId: item?.sizeId?.id,
        price: formatNumber(item.price) || 0,
        quantity: Number(item.quantity) || 0,
        sku: `${code}-${item?.colorId?.name?.toUpperCase()}`,
        status: ProductStatusTab.en.IS_ACTIVE,
      };
    });

    // const productItemsNeedRemove = oldProductItems?.filter((item) => {
    //   return !productItems?.some((productItem) => productItem?.id === item.id);
    // }).map((item) => item.id);
    // const imagesNeedRemove = currentProductImages?.filter((item) => {
    //   return !currentVariantImages?.some((image) => image?.id === item.id);
    // }).map((item) => item.id);
    // const imagesCloudNeedRemove = currentProductImages?.filter((item) => {
    //   return !currentVariantImages?.some((image) => image?.id === item.id);
    // }).map((item) => item.publicId);
    // const images = variants.flatMap((variant) =>
    //   variant.images.map((image) => image)
    // );
    // const imageFiles = variants?.flatMap((variant) =>
    //   variant?.imageFiles?.map((image) => image)
    // );
    // const imagesNeedCreate = images.filter((item) => !item?.id);
    // const isErrorValidateProductItems = productItems.some((item) => !item.price) || variants.some((variant) =>
    //   variant.images.length < IMAGE_MIN_LENGTH) || variants.some((variant) => variant.images.every((image) => !image?.isDefault));
    // if (!isErrorValidateProductItems) {
    const body = {
      ...newData,
      // productItemsNeedRemove,
      // imagesNeedRemove,
      // imagesNeedCreate,
      // imagesCloudNeedRemove,
      categoryIds,
      brandId,
      // id,
      images: productImages,
      productItems,
      status: data.status ? ProductStatusTab.en.IS_ACTIVE : ProductStatusTab.en.UN_ACTIVE,
    };
    console.log(body);
    if (!isEdit) {
      const formData = new FormData();
      productImageFiles.forEach((file) => {
        const blobWithCustomFileName = new Blob([file], { type: 'application/octet-stream' });
        formData.append('files[]', blobWithCustomFileName, file.name);
      });
      formData.append('data', JSON.stringify(body));
      showConfirm('Xác nhận thêm mới sản phẩm?', () =>
        formDataFile(ADMIN_API.product.post, formData, (res) => onFinishSaveProduct(res))
      );
    }
    //   else {
    //     const formData = new FormData();
    //     const imageFilesFiltered = imageFiles.filter((item) => !item?.id);
    //     imageFilesFiltered.forEach((file) => {
    //       const blobWithCustomFileName = new Blob([file], { type: 'application/octet-stream' });
    //       formData.append('files[]', blobWithCustomFileName, file.name);
    //     });
    //     formData.append('data', JSON.stringify(body));
    //     formData.append('_method', 'PUT');
    //     showConfirm("Xác nhận cập nhật sản phẩm?", () => formDataFile(ADMIN_API.product.put, formData, (res) => onFinishSaveProduct(res)));
    //   }
    // }
  };

  const handleCreateVariantProduct = () => {
    const variantOrder = productVarians.length > 0 ? productVarians[productVarians.length - 1].variantOrder + 1 : 0;
    setProductVarians((prev) => [
      ...prev,
      {
        variantOrder,
        sizeId: null,
        colorId: null,
        price: 0,
        quantity: 0,
      },
    ]);
  };

  const handleInputChange = (variantOrder, field, value) => {
    setProductVarians((prev) =>
      prev.map((variant) => (variant.variantOrder === variantOrder ? { ...variant, [field]: value } : variant))
    );
  };

  const handleDeleteVariantProduct = (variantOrderToDelete) => {
    setProductVarians((prev) => prev.filter((item) => item.variantOrder !== variantOrderToDelete));
  };

  const disabledSizeVariant = (sizeId, colorId) => {
    const findVariant = productVarians.find((item) => sizeId === item?.sizeId?.id && colorId === item?.colorId?.id);
    if (findVariant) {
      return true;
    }
    return false;
  }

  useEffect(() => {
    console.log(productVarians);
    console.log(productImageFiles);
    console.log(productImages);
  }, [productVarians, productImageFiles, productImages]);

  const handleDrop = (acceptedFiles) => {
    const newImages = [...productImageFiles];

    acceptedFiles.forEach((file) => {
      const isDuplicate = newImages.some((image) => image.name === file.name);

      if (!isDuplicate) {
        newImages.push(Object.assign(file, { preview: URL.createObjectURL(file) }));
      }
    });

    if (newImages.length > 8) {
      onOpenErrorNotify('Chỉ được phép chọn tối đa 8 ảnh!');
      return;
    }

    console.log(newImages);
    setProductImages(Array.from(newImages));
    setProductImageFiles(Array.from(newImages));
  };

  const handleRemoveAll = () => {
    setProductImages([]);
    setProductImageFiles([]);
  };

  const handleRemove = (file) => {
    const filteredItems = productImages.filter((_file) => _file.path !== file.path);
    setProductImages(filteredItems);

    const filteredItemFiles = productImageFiles.filter((_file) => _file.path !== file.path);
    setProductImageFiles(filteredItemFiles);
  };

  const handleUpdateDefaultImage = (path) => {
    const updatedImages = productImages.map((image) => {
      if (image.path === path) {
        return {
          ...image,
          isDefault: true,
        };
      }
      return {
        ...image,
        isDefault: false,
      };
    });
    setProductImages(updatedImages);
  };

  return (
    <FormProvider methods={methods} onSubmit={handleSubmit(onSubmit)}>
      <Grid container spacing={3}>
        <Grid item xs={12} md={8}>
          <Stack spacing={3}>
            <Card sx={{ p: 3 }} className="card">
              <LabelStyleHeader>Thông tin sản phẩm</LabelStyleHeader>
              <Stack spacing={3} sx={{ py: 2 }}>
                <RHFTextField
                  name="name"
                  topLabel="Tên sản phẩm"
                  placeholder="Nhập tên sản phẩm (tối đa 100 ký tự)"
                  isRequired
                />
                <RHFTextField
                  name="code"
                  topLabel="Mã sản phẩm"
                  placeholder="Nhập mã sản phẩm (tối đa 20 ký tự)"
                  isRequired
                />

                <div>
                  <LabelStyleDescription>Mô tả</LabelStyleDescription>
                  <RHFEditor simple placeholder="Nhập mô tả sản phẩm..." name="description" />
                </div>
              </Stack>
            </Card>

            <Card sx={{ p: 3 }} className="card">
              <Stack direction="row" display="flex" justifyContent="space-between">
                <LabelStyleHeader>Biến thể sản phẩm</LabelStyleHeader>
                <Button size="medium" variant="contained" onClick={handleCreateVariantProduct}>
                  Thêm mới
                </Button>
              </Stack>
              <Stack spacing={3} sx={{ py: 2 }}>
                {productVarians.length <= 0 && <LabelStyleHeader sx={{display: 'flex', justifyContent: 'center'}}>Chưa có dữ liệu</LabelStyleHeader>}
                {productVarians.map((item) => {
                  return (
                    <Box
                      sx={{
                        display: 'flex',
                        gap: 2,
                        alignItems: 'center',
                      }}
                    >
                      <Grid>
                        <LabelStyle>Màu sắc</LabelStyle>
                        <Autocomplete
                          selectOnFocus
                          clearOnBlur
                          style={{ width: '150px' }}
                          handleHomeEndKeys
                          // fullWidth
                          isOptionEqualToValue={(option, value) => option.id === value.id}
                          freeSolo
                          forcePopupIcon
                          popupIcon={<IconArrowDownAutocomplete />}
                          size="small"
                          getOptionLabel={(option) => {
                            if (typeof option === 'string') {
                              return option;
                            }
                            return option.name;
                          }}
                          options={data?.colors || []}
                          onChange={(event, newValue) => {
                            handleInputChange(item.variantOrder, 'colorId', newValue);
                          }}
                          value={item.colorId}
                          renderOption={(props, option) => (
                            <MenuItem
                              {...props}
                              key={option.id}
                              value={option.id}
                              sx={{
                                typography: 'body2',
                                height: 36,
                              }}
                            >
                              <Label variant={'filled'} color={option?.code}>
                                {option?.name}
                              </Label>
                            </MenuItem>
                          )}
                          renderInput={(params) => (
                            <TextField
                              {...params}
                              // error={!!error}
                              // helperText={error?.message}
                              sx={{
                                '& fieldset': {
                                  borderRadius: '6px',
                                },
                                '& .Mui-error': {
                                  marginLeft: 0,
                                },
                              }}
                            />
                          )}
                        />
                      </Grid>

                      <Grid>
                        <LabelStyle>Kích cỡ</LabelStyle>
                        <Autocomplete
                          selectOnFocus
                          clearOnBlur
                          style={{ width: '150px' }}
                          handleHomeEndKeys
                          // fullWidth
                          isOptionEqualToValue={(option, value) => option.id === value.id}
                          freeSolo
                          forcePopupIcon
                          popupIcon={<IconArrowDownAutocomplete />}
                          size="small"
                          getOptionLabel={(option) => {
                            if (typeof option === 'string') {
                              return option;
                            }
                            return option.name;
                          }}
                          options={data?.sizes || []}
                          onChange={(event, newValue) => {
                            handleInputChange(item.variantOrder, 'sizeId', newValue);
                          }}
                          value={item.sizeId}
                          renderOption={(props, option) => {
                            return (
                                <MenuItem
                                    {...props}
                                    key={option.id}
                                    value={option.id}
                                    disabled={disabledSizeVariant(option?.id, item?.colorId?.id)}
                                    sx={{
                                        typography: 'body2',
                                        height: 36,
                                    }}
                                >
                                    <Label variant={'filled'} color={'primary'}>
                                        {option?.name}
                                    </Label>
                                </MenuItem>
                            );
                        }}
                          renderInput={(params) => (
                            <TextField
                              {...params}
                              // error={!!error}
                              // helperText={error?.message}
                              sx={{
                                '& fieldset': {
                                  borderRadius: '6px',
                                },
                                '& .Mui-error': {
                                  marginLeft: 0,
                                },
                              }}
                            />
                          )}
                        />
                      </Grid>

                      <Grid>
                        <LabelStyle>Đơn giá</LabelStyle>
                        <TextField
                          onChange={(e) => {
                            handleInputChange(item.variantOrder, 'price', formatCurrencyVnd(e.target.value));
                          }}
                          value={item.price === 0 ? "" : item.price}
                          placeholder='0'
                          size="small"
                          // error={!!error}
                          // helperText={error?.message}
                          sx={{
                            '& fieldset': {
                              borderRadius: '6px',
                            },
                            '& .Mui-error': {
                              marginLeft: 0,
                            },
                            width: '150px',
                          }}
                        />
                      </Grid>

                      <Grid>
                        <LabelStyle>Số lượng</LabelStyle>
                        <TextField
                          value={item.quantity === 0 ? "" : item.quantity}
                          onChange={(e) => {
                            handleInputChange(item.variantOrder, 'quantity', formatNumber(e.target.value));
                          }}
                          placeholder='0'
                          size="small"
                          // error={!!error}
                          // helperText={error?.message}
                          sx={{
                            '& fieldset': {
                              borderRadius: '6px',
                            },
                            '& .Mui-error': {
                              marginLeft: 0,
                            },
                            width: '150px',
                          }}
                        />
                      </Grid>

                      <Grid>
                        <IconButton
                          sx={{ marginTop: '25px' }}
                          onClick={() => handleDeleteVariantProduct(item.variantOrder)}
                        >
                          <Iconify icon={'eva:trash-2-outline'} width={23} height={23} sx={{ color: 'error.main' }} />
                        </IconButton>
                      </Grid>
                    </Box>
                  );
                })}
              </Stack>
            </Card>

            <Card sx={{ p: 3 }} className="card">
              <Stack spacing={3}>
                <Stack direction="row" display="flex" justifyContent="space-between" sx={{ py: 1 }}>
                  <Stack>
                    <LabelStyle>Hình ảnh sản phẩm</LabelStyle>
                  </Stack>
                  <ProductUploadMultiFile
                    sx={{ width: '75%' }}
                    showPreview
                    accept="image/*"
                    maxSize={3145728}
                    onDrop={handleDrop}
                    onRemove={handleRemove}
                    onRemoveAll={handleRemoveAll}
                    files={productImages}
                    onUpdateImageDefault={handleUpdateDefaultImage}
                    // error={getError()}
                    // helperText={getHelperText()}
                  />
                </Stack>
              </Stack>
            </Card>
          </Stack>
        </Grid>

        <Grid item xs={12} md={4}>
          <Stack spacing={3}>
            <Card sx={{ p: 3 }} className="card">
              <LabelStyleHeader>Phân loại</LabelStyleHeader>
              <Stack spacing={3} sx={{ py: 2 }}>
                <Controller
                  name="categorys"
                  control={control}
                  render={({ field, fieldState: { error } }) => (
                    <Grid>
                      <LabelStyle>
                        Danh mục <span className="required">*</span>
                      </LabelStyle>
                      <Autocomplete
                        {...field}
                        selectOnFocus
                        disableCloseOnSelect
                        multiple
                        clearOnBlur
                        handleHomeEndKeys
                        fullWidth
                        isOptionEqualToValue={(option, value) => option.id === value.id}
                        freeSolo
                        forcePopupIcon
                        popupIcon={<IconArrowDownAutocomplete />}
                        size="small"
                        getOptionLabel={(option) => {
                          if (typeof option === 'string') {
                            return option;
                          }
                          return option.name;
                        }}
                        onChange={(event, newValue) => {
                          field.onChange(newValue);
                        }}
                        options={data?.categories || []}
                        renderOption={(props, option, { selected }) => (
                          <MenuItem
                            {...props}
                            key={option.id}
                            value={option.id}
                            sx={{
                              typography: 'body2',
                              height: 36,
                            }}
                          >
                            <>
                              {option.name}
                              <Checkbox size="small" checked={selected} sx={{ marginLeft: 'auto' }} />
                            </>
                          </MenuItem>
                        )}
                        renderTags={(value, getTagProps) =>
                          value.map((option, index) => (
                            <span key={index} {...getTagProps({ index })}>
                              <Label variant={'ghost'} color={option?.id ? 'primary' : 'default'}>
                                {option?.name}
                              </Label>
                            </span>
                          ))
                        }
                        renderInput={(params) => (
                          <TextField
                            {...params}
                            sx={{
                              '& fieldset': {
                                borderRadius: '6px',
                              },
                              '& .Mui-error': {
                                marginLeft: 0,
                              },
                            }}
                            placeholder={values.categorys?.length === 0 ? 'Chọn danh mục' : ''}
                            error={!!error}
                            helperText={error?.message}
                          />
                        )}
                      />
                    </Grid>
                  )}
                />

                <Controller
                  name="brand"
                  control={control}
                  render={({ field, fieldState: { error } }) => (
                    <Grid>
                      <LabelStyle>
                        Thương hiệu <span className="required">*</span>
                      </LabelStyle>
                      <Autocomplete
                        {...field}
                        selectOnFocus
                        clearOnBlur
                        handleHomeEndKeys
                        fullWidth
                        isOptionEqualToValue={(option, value) => option.id === value.id}
                        freeSolo
                        forcePopupIcon
                        popupIcon={<IconArrowDownAutocomplete />}
                        size="small"
                        getOptionLabel={(option) => {
                          if (typeof option === 'string') {
                            return option;
                          }
                          return option.name;
                        }}
                        options={data?.brands || []}
                        onChange={(event, newValue) => {
                          field.onChange(newValue);
                        }}
                        renderOption={(props, option) => (
                          <MenuItem
                            {...props}
                            key={option.id}
                            value={option.id}
                            sx={{
                              typography: 'body2',
                              height: 36,
                            }}
                          >
                            <>{option.name}</>
                          </MenuItem>
                        )}
                        renderInput={(params) => (
                          <TextField
                            placeholder={'Chọn thương hiệu'}
                            {...params}
                            error={!!error}
                            helperText={error?.message}
                            sx={{
                              '& fieldset': {
                                borderRadius: '6px',
                              },
                              '& .Mui-error': {
                                marginLeft: 0,
                              },
                            }}
                          />
                        )}
                      />
                    </Grid>
                  )}
                />
              </Stack>
            </Card>

            <Card sx={{ p: 3 }} className="card">
              <RHFSwitch
                name="status"
                sx={{ mx: 0, width: 1, justifyContent: 'space-between' }}
                labelPlacement="start"
                label={
                  <>
                    <Typography variant="subtitle2" sx={{ mb: 0.5 }}>
                      Trạng thái
                    </Typography>
                    <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                      {getValues('status') ? ProductStatusTab.vi.IS_ACTIVE : ProductStatusTab.vi.UN_ACTIVE}
                    </Typography>
                  </>
                }
              />
            </Card>

            <Stack spacing={2} direction="row">
              <Button onClick={handleCancel} size="medium" sx={{ width: '100%' }} color="inherit" variant="contained">
                Hủy
              </Button>
              <Button
                type="submit"
                sx={{ width: '100%' }}
                size="medium"
                variant="contained"
                disabled={isEdit && !isNotDefaultEdit}
              >
                Lưu
              </Button>
            </Stack>
          </Stack>
        </Grid>
      </Grid>

      <Stack sx={{ mt: 3 }}>
        <Divider />
        <Stack spacing={2} direction="row" display="flex" justifyContent="flex-end" sx={{ mt: 2 }}>
          <Button onClick={handleCancel} size="medium" color="inherit" variant="contained">
            Hủy
          </Button>
          <Button type="submit" size="medium" variant="contained" disabled={!isNotDefaultEdit && isEdit}>
            Lưu
          </Button>
        </Stack>
      </Stack>
    </FormProvider>
  );
}
