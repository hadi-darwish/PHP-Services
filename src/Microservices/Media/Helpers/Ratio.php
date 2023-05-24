<?php
	namespace DigitalSplash\Media\Helpers;

	use DigitalSplash\Exceptions\InvalidParamException;
	use DigitalSplash\Exceptions\UploadException;
	use Intervention\Image\ImageManager;
	use DigitalSplash\Helpers\Helper;
	use DigitalSplash\Media\Interface\IImageModify;
	use DigitalSplash\Media\Models\ImagesExtensions;

	class Ratio implements IImageModify {
		private string $source;
		private float $ratio;
		private string $destination;
		private bool $addCanvas;
		private string|null $canvasColor;
		private int|float $width;
		private int|float $height;
		private string $extension;

		public function __construct(
			string $source,
			float $ratio = 0,
			string $destination = '',
			bool $addCanvas = false,
			string $canvasColor = null,
		) {
			$this->source = $source;
			$this->ratio = $ratio;
			$this->destination = $destination;
			$this->addCanvas = $addCanvas;
			$this->canvasColor = $canvasColor;
			$this->extension = strtolower(pathinfo($this->source, PATHINFO_EXTENSION));
		}

		public function validateParams(): void {
			if (Helper::IsNullOrEmpty($this->source)) {
				throw new InvalidParamException("source");
			}
			if (!file_exists($this->source)) {
				throw new UploadException("Source file does not exist!");
			}
			if (!in_array($this->extension, $allowedExtensions = ImagesExtensions::getExtensions())) {
				$allowed = implode(", ", $allowedExtensions);
				throw new UploadException("File extension is not allowed! Allowed extensions: $allowed");
			}
		}

		private function canvasColorSetter(): void {
			switch ($this->extension) {
				case 'png':
					$this->canvasColor = $this->canvasColor ?? null;
					break;
				default:
					$this->canvasColor = $this->canvasColor ?? '#ffffff';
					break;
			}
		}

		private function calculateDimensionsWithCanvas(float $width, float $height): void {
			$ratio = $width / $height;

			if ($ratio === $this->ratio) {
				return;
			}

			if ($ratio > $this->ratio) {
				$targetHeight = $width / $this->ratio;
				$this->height = $height + 2 * (($targetHeight - $height) / 2);
				$this->width = $width;
			} else  {
				$targetWidth = $height * $this->ratio;
				$this->width = $width + 2 * (($targetWidth - $width) / 2);
				$this->height = $height;
			}

			$this->canvasColorSetter();
		}

		private function calculateDimensionsWithoutCanvas(float $width, float $height): void {
			$ratio = $width / $height;

			if ($ratio === $this->ratio) {
				return;
			}

			if ($ratio > $this->ratio) {
				$this->height = $width / $this->ratio;
				$this->width = $width;
			} else  {
				$this->width = $height * $this->ratio;
				$this->height = $height;
			}
		}

		public function save(): void {
			$this->validateParams();
			$manager = new ImageManager([
				'driver' => 'gd'
			]);
			$image = $manager->make($this->source);
			$width = $image->width();
			$height = $image->height();

			if ($this->addCanvas) {
				$this->calculateDimensionsWithCanvas($width, $height);
				$image->resizeCanvas($this->width, $this->height, 'center', false, $this->canvasColor);
			} else {
				$this->calculateDimensionsWithoutCanvas($width, $height);
				$image->resize($this->width, $this->height);
			}

			Helper::CreateFolderRecursive(pathinfo($this->destination, PATHINFO_DIRNAME));

			$image->save($this->destination);
		}
	}
