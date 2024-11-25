import Box from '@mui/material/Box';
import Link from '@mui/material/Link';
import Card from '@mui/material/Card';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';

// ----------------------------------------------------------------------

export type ProductItemProps = {
  id: number;
  title: string;
  price: number;
  image: string;
};

export function ProductItem({ product }: { product: ProductItemProps }) {
  return (
    <Card>
      <Box sx={{ pt: '100%', position: 'relative' }}>
        <Box
          component="img"
          alt={product.title}
          src={`http://localhost:8000/uploads/products/${product.image}`}
          sx={{
            top: 0,
            width: 1,
            height: 1,
            objectFit: 'cover',
            position: 'absolute',
          }}
        />
      </Box>

      <Stack spacing={2} sx={{ p: 3 }}>
        <Link color="inherit" underline="hover" variant="subtitle2" noWrap>
          {product.title}
        </Link>

        <Typography variant="subtitle1">${product.price.toFixed(2)}</Typography>
      </Stack>
    </Card>
  );
}
