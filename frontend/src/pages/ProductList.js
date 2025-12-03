import React, { useState, useEffect } from 'react';
import {
  Container,
  Grid,
  Card,
  CardContent,
  CardActions,
  Typography,
  Button,
  IconButton,
  Box,
  CircularProgress,
  Alert,
  Fab,
} from '@mui/material';
import {
  Add as AddIcon,
  Edit as EditIcon,
  Delete as DeleteIcon,
} from '@mui/icons-material';
import { productService } from '../services/api';
import ProductDialog from '../components/ProductDialog';
import DeleteConfirmDialog from '../components/DeleteConfirmDialog';

const ProductList = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [productToDelete, setProductToDelete] = useState(null);

  const fetchProducts = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await productService.getAllProducts();
      setProducts(data);
    } catch (err) {
      setError('Failed to load products. Please try again.');
      console.error('Error fetching products:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  const handleAddClick = () => {
    setSelectedProduct(null);
    setDialogOpen(true);
  };

  const handleEditClick = (product) => {
    setSelectedProduct(product);
    setDialogOpen(true);
  };

  const handleDeleteClick = (product) => {
    setProductToDelete(product);
    setDeleteDialogOpen(true);
  };

  const handleDialogClose = () => {
    setDialogOpen(false);
    setSelectedProduct(null);
  };

  const handleDeleteDialogClose = () => {
    setDeleteDialogOpen(false);
    setProductToDelete(null);
  };

  const handleSave = async (productData) => {
    try {
      if (selectedProduct) {
        await productService.updateProduct(selectedProduct.id, productData);
      } else {
        await productService.createProduct(productData);
      }
      await fetchProducts();
      handleDialogClose();
    } catch (err) {
      console.error('Error saving product:', err);
      throw err;
    }
  };

  const handleDelete = async () => {
    try {
      await productService.deleteProduct(productToDelete.id);
      await fetchProducts();
      handleDeleteDialogClose();
    } catch (err) {
      setError('Failed to delete product. Please try again.');
      console.error('Error deleting product:', err);
    }
  };

  if (loading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="80vh">
        <CircularProgress />
      </Box>
    );
  }

  return (
    <Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h4" component="h1">
          Product Catalog
        </Typography>
      </Box>

      {error && (
        <Alert severity="error" sx={{ mb: 3 }} onClose={() => setError(null)}>
          {error}
        </Alert>
      )}

      {products.length === 0 ? (
        <Box textAlign="center" py={8}>
          <Typography variant="h6" color="textSecondary" gutterBottom>
            No products found
          </Typography>
          <Typography variant="body2" color="textSecondary" paragraph>
            Start by adding your first product
          </Typography>
          <Button
            variant="contained"
            startIcon={<AddIcon />}
            onClick={handleAddClick}
          >
            Add Product
          </Button>
        </Box>
      ) : (
        <Grid container spacing={3}>
          {products.map((product) => (
            <Grid item xs={12} sm={6} md={4} key={product.id}>
              <Card elevation={2}>
                <CardContent>
                  <Typography variant="h6" component="h2" gutterBottom>
                    {product.name}
                  </Typography>
                  <Typography variant="body2" color="textSecondary" paragraph>
                    {product.description}
                  </Typography>
                  <Typography variant="h5" color="primary" gutterBottom>
                    ${product.price.toFixed(2)}
                  </Typography>
                  <Typography variant="body2" color="textSecondary">
                    Stock: {product.stock} units
                  </Typography>
                </CardContent>
                <CardActions>
                  <IconButton
                    size="small"
                    color="primary"
                    onClick={() => handleEditClick(product)}
                  >
                    <EditIcon />
                  </IconButton>
                  <IconButton
                    size="small"
                    color="error"
                    onClick={() => handleDeleteClick(product)}
                  >
                    <DeleteIcon />
                  </IconButton>
                </CardActions>
              </Card>
            </Grid>
          ))}
        </Grid>
      )}

      <Fab
        color="primary"
        aria-label="add"
        sx={{ position: 'fixed', bottom: 16, right: 16 }}
        onClick={handleAddClick}
      >
        <AddIcon />
      </Fab>

      <ProductDialog
        open={dialogOpen}
        product={selectedProduct}
        onClose={handleDialogClose}
        onSave={handleSave}
      />

      <DeleteConfirmDialog
        open={deleteDialogOpen}
        productName={productToDelete?.name}
        onClose={handleDeleteDialogClose}
        onConfirm={handleDelete}
      />
    </Container>
  );
};

export default ProductList;
