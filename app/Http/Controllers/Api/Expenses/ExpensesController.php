<?php

namespace App\Http\Controllers\Api\Expenses;

use App\DTOs\Expenses\ExpenseDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\{ExpensesDestroyRequest, ExpensesShowRequest, ExpensesStoreRequest, ExpensesUpdateRequest};
use App\Http\Resources\{ExpenseResource, ExpensesResource};
use App\Services\Expenses\ExpensesService;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Expenses',
    description: 'Expenses API'
)]
class ExpensesController extends Controller
{
    private ExpensesService $expensesService;

    public function __construct(ExpensesService $expensesService)
    {
        $this->expensesService = $expensesService;
    }

    #[OA\Get(
        path: '/expenses',
        summary: 'Display a listing of the expenses of the owner.',
        tags: ['Expenses'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Object response `Data` content `Expenses` property'
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request'
            ),
        ]
    )]
    public function index()
    {
        $expenses = $this->expensesService->getAll(auth()->user()->id);

        return (new ExpensesResource($expenses))->response()->setStatusCode(Response::HTTP_OK);
    }

    #[OA\Post(
        path: '/expenses',
        summary: 'Store a newly created expense in database.',
        requestBody: new OA\RequestBody(
            description: 'Input data',
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        required: ['description', 'date_registration', 'value'],
                        properties: [
                            new OA\Property(
                                property: 'description',
                                description: 'Description to be created',
                                type: 'string',
                                maxLength: 191,
                            ),
                            new OA\Property(
                                property: 'date_registration',
                                description: 'Date registration to be created',
                                type: 'string'
                            ),
                            new OA\Property(
                                property: 'value',
                                description: 'Value to be created',
                                type: 'number',
                                minLength: 0
                            ),
                        ],
                        type: 'object'
                    )
                ),
            ]
        ),
        tags: ['Expenses'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Object response `Data` content `Expenses` property'
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 422, description: 'The given data was invalid'),
        ]
    )]
    public function store(ExpensesStoreRequest $request)
    {
        $expenseDTO = new ExpenseDTO(
            $request->input('description'),
            $request->input('date_registration'),
            $request->input('value')
        );

        $expense = $this->expensesService->create($expenseDTO, auth()->user()->id);

        return (new ExpenseResource($expense))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/expenses/{id}',
        summary: 'Display the specified expense.',
        tags: ['Expenses'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Object response `Data` content `Expenses` property'
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 404, description: 'Page not found'),
        ]
    )]
    public function show(ExpensesShowRequest $request, $id)
    {
        $expense = $this->expensesService->findById($id);

        if (!$expense) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_BAD_REQUEST);
        }

        if ($request->user()->cannot('view', [Expenses::class, $expense])) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_UNAUTHORIZED);
        }

        return (new ExpenseResource($expense))->response()->setStatusCode(Response::HTTP_OK);
    }

    #[OA\Patch(
        path: '/expenses/{id}',
        summary: 'Update the specified expense in the database.',
        requestBody: new OA\RequestBody(
            description: 'Input data',
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        required: ['description', 'date_registration', 'value'],
                        properties: [
                            new OA\Property(
                                property: 'id',
                                description: 'Id of the expense created',
                                type: 'number'
                            ),
                            new OA\Property(
                                property: 'description',
                                description: 'Description created',
                                type: 'string',
                                maxLength: 191,
                            ),
                            new OA\Property(
                                property: 'date_registration',
                                description: 'Date registration created',
                                type: 'string'
                            ),
                            new OA\Property(
                                property: 'value',
                                description: 'Value created',
                                type: 'number',
                                minLength: 0
                            ),
                        ],
                        type: 'object'
                    )
                ),
            ]
        ),
        tags: ['Expenses'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Object response `Data` content `Expenses` property'
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'The given data was invalid'),
        ]
    )]
    public function update(ExpensesUpdateRequest $request, $id)
    {
        $expense = $this->expensesService->findById($id);

        if (!$expense) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_BAD_REQUEST);
        }

        if ($request->user()->cannot('update', [Expenses::class, $expense])) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_UNAUTHORIZED);
        }

        $expenseDTO = new ExpenseDTO(
            $request->input('description'),
            $request->input('date_registration'),
            $request->input('value')
        );

        $expense = $this->expensesService->update($expenseDTO, $id, auth()->user()->id);

        return (new ExpenseResource($expense))->response()->setStatusCode(Response::HTTP_OK);
    }

    #[OA\Delete(
        path: '/expenses/{id}',
        summary: 'Remove the specified expense in the database.',
        tags: ['Expenses'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'No content'
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Page not found'),
        ]
    )]
    public function destroy(ExpensesDestroyRequest $request, $id)
    {
        $expense = $this->expensesService->findById($id);

        if (!$expense) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_BAD_REQUEST);
        }

        if ($request->user()->cannot('delete', [Expenses::class, $expense])) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_UNAUTHORIZED);
        }

        $this->expensesService->delete($id, auth()->user()->id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
