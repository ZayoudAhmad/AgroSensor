import Toolbar from '@mui/material/Toolbar';
import OutlinedInput from '@mui/material/OutlinedInput';
import InputAdornment from '@mui/material/InputAdornment';
import { Iconify } from 'src/components/iconify';

type CropsTableToolbarProps = {
  filterName: string;
  onFilterName: (event: React.ChangeEvent<HTMLInputElement>) => void;
};

export function CropsTableToolbar({ filterName, onFilterName }: CropsTableToolbarProps) {
  return (
    <Toolbar sx={{ height: 96, p: (theme) => theme.spacing(0, 1, 0, 3) }}>
      <OutlinedInput
        value={filterName}
        onChange={onFilterName}
        placeholder="Search crops..."
        startAdornment={
          <InputAdornment position="start">
            <Iconify width={20} icon="eva:search-fill" sx={{ color: 'text.disabled' }} />
          </InputAdornment>
        }
        sx={{ maxWidth: 320 }}
      />
    </Toolbar>
  );
}
