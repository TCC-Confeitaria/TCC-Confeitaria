CREATE DATABASE  IF NOT EXISTS `confeitaria_tcc` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `confeitaria_tcc`;
-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: localhost    Database: confeitaria_tcc
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `itens_cardapio`
--

DROP TABLE IF EXISTS `itens_cardapio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens_cardapio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) NOT NULL DEFAULT 'padrao.png',
  `categoria` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itens_cardapio`
--

LOCK TABLES `itens_cardapio` WRITE;
/*!40000 ALTER TABLE `itens_cardapio` DISABLE KEYS */;
INSERT INTO `itens_cardapio` VALUES (5,'Copo Surpresa de Morango','Delicioso copo da felicidade com camadas de brigadeiro e creme de chocolate branco, além de uma geléia de morango que traz o equilíbrio entre a doçura e a acidez',19.99,'6a1e29662a3e4.jpg','Copos da Felicidade','ativo'),(6,'Cento de Docinhos - (Somente 2 sabores)','Os queridinhos de qualquer festa, nosso cento dois sabores conta com os clássicos brigadeiro(50 unidades) e beijinho(50 unidades), ideias para agradar qualquer um!',80.00,'6a1e2a957be58.jpg','Festa','ativo'),(7,'Meio Cento de Docinhos - (Somente 2 sabores)','Os queridinhos de qualquer festa, nosso cento dois sabores conta com os clássicos brigadeiro(25 unidades) e beijinho(25 unidades), ideias para agradar qualquer um!',40.00,'6a1e2ad47032d.jpg','Festa','ativo'),(8,'Cento de Salgados','Os queridinhos de qualquer festa, nosso cento de salgados conta com sabores variados, ideias para agradar qualquer um!',90.00,'6a1e3176f399f.jpg','Festa','ativo'),(9,'Torta de Limão','A nossa Torta de Limão é o clássico que nunca falha. Combinamos uma massa leve e crocante com um recheio cremoso e aveludado de limão, que traz o azedinho na medida exata. Para finalizar com chave de ouro, uma generosa camada de chantilly',50.00,'6a1e321972f7b.jpg','Tortas','ativo'),(10,'Torta de Morango','Para os apaixonados por doçura e frescor, a nossa Torta de Morango é um verdadeiro espetáculo. Ela traz uma base crocante e amanteigada, recheada com um creme de confeiteiro suave e baunilhado. Por cima, uma cobertura farta de morangos frescos, selecionados e brilhantes, finalizados com uma leve geleia de brilho artesanal.',50.00,'6a1e3261c99b6.jpg','Tortas','ativo');
/*!40000 ALTER TABLE `itens_cardapio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `tipo` enum('cliente','admin') DEFAULT 'cliente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (2,'Marcelo Martins','mfran.martiins@gmail.com','(11) 91235-3101','$2y$10$Y0gCbkFgcDv1zIlhHlgRJuaLHedBGfu4Q8bmRwBM2GlW/FJ2eDpoG','2007-07-02','428.681.868-31','Rua Constância Asson','394','Jardim Três Marias','São Paulo','cliente'),(3,'Admin Mestre','admin@confeitaria.com',NULL,'$2y$10$MXaD7T0X8/qIMhxcWMcfFe.6F4mzmSh1NvNjEmy0D4RTqHZ67yj1W',NULL,NULL,NULL,NULL,NULL,NULL,'admin'),(4,'Marcelo Martins','marcelofranciscomartins07@gmail.com','(11) 91235-3101','$2y$10$MQ4K4hPC2ShXScbDhCz4rOJnKXhy4LAjCbPNGgnxwTzd/fctY4gZ2','2007-07-02','428.681.868-31','Rua Constância Asson','394','Jardim Três Marias','São Paulo','cliente'),(5,'Marcelo Martins','teste@gmail.com','11912353101','$2y$10$xhByJ7eqNGDcUf6JpN89N.pNp.an6DXpRq3O9dmcEi8Hpjj2CmMSy','2007-07-02','12345678911','Rua Constância Asson','445','Jardim Três Marias','São Paulo','cliente');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-01 22:36:25
