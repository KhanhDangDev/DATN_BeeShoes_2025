import { useState, useEffect } from "react";
import PropTypes from 'prop-types';
import { Stack, FormControlLabel, InputAdornment, TextField, MenuItem, Button, FormControl, InputLabel, Select, Checkbox, ListItemText, OutlinedInput, Chip } from '@mui/material';
// components
import { IconArrowDown, IconArrowUp } from "../../../../components/IconArrow";
import Iconify from '../../../../components/Iconify';
import useDebounce from "../../../../hooks/useDebounce";
// ----------------------------------------------------------------------

const INPUT_WIDTH = 300;
const FORM_CONTROL_WIDTH = 500;
const MIN_FORM_CONTROL_WIDTH = 1;

ProductTableToolbar.propTypes = {
  optionsStock: PropTypes.array,
  optionsBrand: PropTypes.array,
  optionsCategory: PropTypes.array,
  filterStock: PropTypes.arrayOf(PropTypes.string),
  onFilterSearch: PropTypes.func,
  onFilterStock: PropTypes.func,
  onFilterBrand: PropTypes.func,
  onFilterCategory: PropTypes.func,
};

export default function ProductTableToolbar({
  optionsStock,
  optionsBrand,
  optionsCategory,
  filterStock,
  filterBrand,
  filterCategory,
  onFilterSearch,
  onFilterStock,
  onFilterBrand,
  onFilterCategory,
}) {

  const [filterSearch, setFilterSearch] = useState('');

  const debounceValue = useDebounce(filterSearch, 500);

  useEffect(() => {
    onFilterSearch(debounceValue);
  }, [debounceValue]);

  return (
    <Stack spacing={2} direction={{ xs: 'column', md: 'row' }} sx={{ py: 2.5, px: 2 }}>
      <FormControl sx={{ m: MIN_FORM_CONTROL_WIDTH, width: FORM_CONTROL_WIDTH }}>
        <InputLabel>Tình trạng</InputLabel>
        <Select
          multiple
          value={filterStock}
          onChange={onFilterStock}
          input={<OutlinedInput label="Tình trạng" />}
          IconComponent={(props) => {
            if (props.className.includes('MuiSelect-iconOpen')) {
              return <IconArrowUp />;
            }
            return <IconArrowDown />;
          }}
          renderValue={(selected) => selected.join(', ')}
          sx={{
            maxWidth: { md: INPUT_WIDTH },
            textTransform: 'capitalize',
          }}
        >
          {optionsStock.map((s) => (
            <MenuItem
              key={s}
              value={s}
              sx={{
                mx: 1,
                my: 0.5,
                borderRadius: 0.75,
                typography: 'body2',
                textTransform: 'capitalize',
                padding: 0,
              }}
            >
              <Checkbox size='small' checked={filterStock.indexOf(s) > -1} />
              {s}
            </MenuItem>
          ))}
        </Select>
      </FormControl>

      <FormControl sx={{ m: MIN_FORM_CONTROL_WIDTH, width: FORM_CONTROL_WIDTH }}>
        <InputLabel>Danh mục</InputLabel>
        <Select
          multiple
          value={filterCategory}
          onChange={onFilterCategory}
          input={<OutlinedInput label="Danh mục" />}
          IconComponent={(props) => {
            if (props.className.includes('MuiSelect-iconOpen')) {
              return <IconArrowUp />;
            }
            return <IconArrowDown />;
          }}
          renderValue={(selected) => {
            const selectedCategory = optionsCategory.filter(c => selected.includes(c.id));
            return selectedCategory.map(c => c.name).join(', ');
          }}
          sx={{
            maxWidth: { md: INPUT_WIDTH },
            textTransform: 'capitalize',
          }}
        >
          {optionsCategory?.map((category) => (
            <MenuItem
              key={category.id}
              value={category.id}
              sx={{
                mx: 1,
                my: 0.5,
                borderRadius: 0.75,
                typography: 'body2',
                textTransform: 'capitalize',
                padding: 0,
              }}
            >
              <Checkbox size='small' checked={filterCategory.indexOf(category.id) > -1} />
              {category.name}
            </MenuItem>
          ))}
        </Select>
      </FormControl>

      <FormControl sx={{ m: MIN_FORM_CONTROL_WIDTH, width: FORM_CONTROL_WIDTH }}>
        <InputLabel>Thương hiệu</InputLabel>
        <Select
          multiple
          value={filterBrand}
          onChange={onFilterBrand}
          input={<OutlinedInput label="Thương hiệu" />}
          IconComponent={(props) => {
            if (props.className.includes('MuiSelect-iconOpen')) {
              return <IconArrowUp />;
            }
            return <IconArrowDown />;
          }}
          renderValue={(selected) => {
            const selectedBrands = optionsBrand.filter(brand => selected.includes(brand.id));
            return selectedBrands.map(brand => brand.name).join(', ');
          }}
          sx={{
            maxWidth: { md: INPUT_WIDTH },
            textTransform: 'capitalize',
          }}
        >
          {optionsBrand?.map((brand) => (
            <MenuItem
              key={brand.id}
              value={brand.id}
              sx={{
                mx: 1,
                my: 0.5,
                borderRadius: 0.75,
                typography: 'body2',
                textTransform: 'capitalize',
                padding: 0,
              }}
            >
              <Checkbox size='small' checked={filterBrand.indexOf(brand.id) > -1} />
              {brand.name}
            </MenuItem>
          ))}
        </Select>
      </FormControl>

      <TextField
        fullWidth
        value={filterSearch}
        onChange={(event) => { setFilterSearch(event.target.value) }}
        placeholder="Tìm kiếm sản phẩm..."
        InputProps={{
          startAdornment: (
            <InputAdornment position="start">
              <Iconify icon={'eva:search-fill'} sx={{ color: 'text.disabled', width: 20, height: 20 }} />
            </InputAdornment>
          ),
        }}
      />
    </Stack>
  );
}
