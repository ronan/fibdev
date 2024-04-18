<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark" />
  <link href="./main.css" rel="stylesheet">
  <title>{{project}} [Backdev Report]</title>
</head>

<body>
  <header class="bg-gray-800">
    <nav class="container mx-auto px-6 py-3">
      <div class="flex items-center justify-between">
        <div class="text-white font-bold text-xl">
          <a href="#">Backdev</a>
        </div>
        <div class="">
          <ul class="flex items-center space-x-8">
            <li><a href="#" class="text-white">Home</a></li>
          </ul>
        </div>
      </div>    
    </nav>
  </header>
  <main class="container-fluid">
    <article class="component">
      <div class="flex min-h-screen justify-center bg-gray-100">
        <div class="mx-auto px-5 m-5">
          {{#snapshots}}
            <div class="max-w-xs rounded-lg text-gray-500 bg-white p-2 shadow duration-150 hover:shadow-md relative">              
              <img class="w-full rounded-lg object-cover object-center" src="screenshot.png" alt="product" />
              <div class="px-4">
                <div class="my-4 flex items-center justify-between">
                  <h3 class="font-bold text-gray-500">{{project}}</h3>
                </div>
                <div class="flex text-xs items-center justify-between">
                  <p class="font-semibold text-gray-500 w-24 truncate">#{{commit}}</p>
                  <p class="rounded-full bg-blue-500  font-semibold text-white px-2">{{env}}</p>
                  <p class="font-semibold text-gray-500" title="{{date_rel}}">
                    <a href="snapshots/{{date_path}}/clone/">{{date_abs}}</a>
                  </p>
                </div>
                <div class="my-4 flex items-center justify-between">
                  <p class="text-sm font-semibold text-gray-500">CMS</p>
                  <p class="rounded-full bg-blue-500 px-2 py-0.5 text-xs font-semibold text-white">{{kind}}</p>
                </div>
                <div class="my-4 flex items-center justify-between">
                  <p class="text-sm font-semibold text-gray-500">Core Version</p>
                  <p class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-600">{{version}}</p>
                </div>
                <div class="my-4 flex items-center justify-between">
                  <p class="text-sm font-semibold text-gray-500">Pages Captured</p>
                  <p class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-600">{{pages}}</p>
                </div>
                <div class="my-4 flex items-center justify-between">
                  <p class="text-sm font-semibold text-gray-500">VRT Failures</p>
                  <p class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-600"><a href="snapshots/{{date_path}}/report">{{failures}}</a></p>
                </div>
              </div>
            </div>
          {{/snapshots}}

        </div>
      </div>
    </article>
  </main>
  <footer></footer>
</body>
</html>