<?php

namespace RootNameSpace\Belajar\PHP\MVC\Service;

use RootNameSpace\Belajar\PHP\MVC\Domain\Author;
use RootNameSpace\Belajar\PHP\MVC\Exception\ValidationException;
use RootNameSpace\Belajar\PHP\MVC\Model\AuthorRequest;
use RootNameSpace\Belajar\PHP\MVC\Model\AuthorResponse;
use RootNameSpace\Belajar\PHP\MVC\Model\BooksListResponse;
use RootNameSpace\Belajar\PHP\MVC\Repository\AuthorRepository;

class AuthorService
{
    private AuthorRepository $repository;

    public function __construct(AuthorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findAll(int $page = 1, int $limit = 50): array
    {
        $authors = $this->repository->findAll($page, $limit);

        if ($authors != null) {
            return $authors;
        } else {
            throw new ValidationException("Data not found");
        }
    }

    public function add(AuthorRequest $request): AuthorResponse
    {
        $this->validateAuthorRequest($request);

        try {
            $author = new Author(
                name: $request->name,
                email: $request->email,
                birthdate: $request->birthdate,
                placeOfBirth: $request->placeOfBirth
            );

            $response = $this->repository->save($author);
        } catch (ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }
        return new AuthorResponse($response);
    }

    private function validateAuthorRequest(AuthorRequest $request)
    {
        switch ($request) {
            case $request->name == trim(""):
            case $request->email == trim(""):
            case $request->birthdate == trim(""):
            case $request->placeOfBirth == trim(""):
            case $request->name == null:
            case $request->email == null:
            case $request->birthdate == null:
            case $request->placeOfBirth == null:
                throw new ValidationException("There must be no empty fields");
        }
    }

    public function removeById(string $id)
    {
        $find = $this->repository->findById($id);

        if ($find) {
            $this->repository->remove($id);
        } else {
            throw new ValidationException('Id Not Found');
        }
    }

    public function getById(int $id): Author
    {
        if ($author = $this->repository->findById($id)) {
            return $author;
        } else {
            throw new ValidationException("Data not found");
        }
    }

    public function updateById(int $id, AuthorRequest $request): AuthorResponse
    {
        $this->validateAuthorRequest($request);
        $find = $this->repository->findById($id);

        if (!$find) {
            throw new ValidationException('Id Not Found');
        }

        try {
            $author = new Author(
                id: $id,
                name: $request->name,
                email: $request->email,
                birthdate: $request->birthdate,
                placeOfBirth: $request->placeOfBirth
            );

            $response = $this->repository->update($author);
            return new AuthorResponse($response);
        } catch (ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }
    }
}
